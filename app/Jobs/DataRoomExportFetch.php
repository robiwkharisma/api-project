<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Repositories\Entities\User;
use App\Repositories\Entities\Locale;
use App\Repositories\Entities\DataRoom;
use App\Repositories\Entities\DataRoomExport;
use App\Repositories\Entities\DataRoomExportNode;

use DB;
use Mail;
use ZipArchive;
use SplFileInfo;

class DataRoomExportFetch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataRoomExportNode;
    public $propertyLocale;

    /**
     * Create a new job instance.
     *
     * @param $attributes
     * @return void
     */
    public function __construct(DataRoomExportNode $dataRoomExportNode, $propertyLocale)
    {
        $this->dataRoomExportNode = $dataRoomExportNode;
        $this->propertyLocale = $propertyLocale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            DB::beginTransaction();

            $nodeId = $this->dataRoomExportNode->dataroom_id;
            
            $dataroom = DataRoom::getDataroomForExportFetch($nodeId, $this->propertyLocale);
            
            $dataroomExport = DataRoomExport::find($this->dataRoomExportNode->dataroom_export_id);

            if($dataroomExport->status != DataRoomExport::STATUS_900_DONE){
                $this->dataRoomExportNode->status = DataRoomExportNode::STATUS_100_INPROGRESS;
                $this->dataRoomExportNode->save();

                $dirr = [];
                $filePathName = "";
                if(empty($dataroom->isFolder)){
                    if(!empty($dataroom->filePath)){
                        $fileName = $dataroom->filePath; #-- just for time being
                        $filePathName = $dataroom->level.' '.$dataroom->label;
                    }
                }else{
                    $dirr = [$dataroom->level.' '.$dataroom->label];
                }
                
                $tempDirr = [];                
                while(!empty($dataroom->parentId)){
                    $dataroom = DataRoom::getDataroomForExportFetch($dataroom->parentId, $this->propertyLocale);
                    
                    array_push($tempDirr, $dataroom->level.' '.$dataroom->label);    
                }
                $dirr = array_merge($dirr, $tempDirr);
                
                $makeDir = implode('/',array_reverse($dirr, true));
                $filePath = storage_path(DataRoomExport::EXPORT_FETCH_PATH.$dataroomExport->hash.'/'.$makeDir);
                
                if(!file_exists($filePath)){
                    mkdir($filePath, 0777,true);
                }

                if(isset($fileName)){
                    $fileInfo = new SplFileInfo($fileName);
                    $fileExtension = $fileInfo->getExtension();
                    
                    $rawFile = file_get_contents($fileName);
                    if($rawFile)
                    {
                        file_put_contents($filePath.'/'.$filePathName.'.'.$fileExtension, $rawFile);
                    }
                }

                $destinationPath = storage_path(DataRoomExport::EXPORT_ARCHIVE_PATH.$dataroomExport->hash.'.zip');
                
                if(!file_exists(storage_path(DataRoomExport::EXPORT_ARCHIVE_PATH))){
                    mkdir(storage_path(DataRoomExport::EXPORT_ARCHIVE_PATH), 0777,true);
                }

                $this->dataRoomExportNode->status = DataRoomExportNode::STATUS_900_DONE;
                $this->dataRoomExportNode->save();

                $dataroomExportNodeStatus = DataRoomExportNode::where('dataroom_export_id', $dataroomExport->id)->pluck('status')->all();

                if(count(array_unique($dataroomExportNodeStatus)) ===1 && end($dataroomExportNodeStatus) === DataRoomExportNode::STATUS_900_DONE){
                    
                    $sources    = storage_path(DataRoomExport::EXPORT_FETCH_PATH.$dataroomExport->hash);
                    $basePath   = $dataroomExport->hash;
                    
                    $this->createZip($sources, $destinationPath, $basePath);

                    $dataroomExport->archive = $dataroomExport->hash.'.zip';
                    $dataroomExport->status  = DataRoomExport::STATUS_900_DONE;
                    $dataroomExport->save();

                    $user = User::find($dataroomExport->user_id);

                    Mail::to($user->email)->queue(new \App\Mail\DataRoomExported($dataroomExport));
                    
                }else{
                    $dataroomExport->status = DataRoomExport::STATUS_101_INPROGRESS_FETCHING;
                    $dataroomExport->save();
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
    }

    /**
     * createZip
     * @param  [string] $file
     * @param  [string] $destination
     * @param  [boolean] $destination
     * @return [boolean]
     */
    public function createZip($sources, $destination, $pathInFile) {
        $zip = new ZipArchive();
        if($zip->open($destination,ZIPARCHIVE::CREATE) !== true) {
            return false;
        }

        $zip->open($destination, ZIPARCHIVE::CREATE);
        $zip = $this->zipRecursive($sources, $zip, $pathInFile);
        $zip->close();
    }

    public function zipRecursive($sources, $zip, $pathInFile=false) {
        if (!file_exists($sources) OR !extension_loaded('zip')) 
        {
            return false;
        }
        
        if (!$pathInFile) 
        {
            $pathInFile = $sources;
        }
        
        $pathInFile = trim($pathInFile, '/');
        $zip->addEmptyDir($pathInFile);
        $dir = opendir($sources);
        while (false !== ($file = readdir($dir))) {
            if ($file == '.' OR $file == '..') {continue;}

            if (is_dir($sources . '/' . $file)) {
                $this->zipRecursive($sources . '/' . $file, $zip, $pathInFile . '/' . $file);
            } else {
                $zip->addFile($sources . '/' . $file, $pathInFile . '/' . $file);
            }
        }
        return $zip;
    }
}
