<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Repositories\Entities\DataRoom;
use App\Repositories\Entities\DataRoomExport;
use App\Repositories\Entities\DataRoomExportNode;
use App\Repositories\Entities\Property;
use App\Repositories\Entities\Locale;
use DB;

class DataRoomExportGenerate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataroom_export;

    /**
     * Create a new job instance.
     *
     * @param DataRoomExport $dataroom_export;
     * @return void
     */
    public function __construct(DataRoomExport $dataroom_export)
    {
        $this->dataroom_export = $dataroom_export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $this->dataroom_export->setStatus(DataRoomExport::STATUS_100_INPROGRESS);
            $this->dataroom_export->save();

            $nodeIds = json_decode($this->dataroom_export->content);
            $propertyLocale = $this->dataroom_export->getPropertyLocaleCode();

            foreach ($nodeIds as $nodeId) {
                $dataroom = DataRoom::select('id')
                    ->where('property_id', $this->dataroom_export->property_id)
                    ->find($nodeId);

                if ($dataroom) {
                    $exportNodeExist = DataRoomExportNode::where('dataroom_export_id', $this->dataroom_export->id)
                        ->where('dataroom_id', $dataroom->id)
                        ->first();

                    if (!$exportNodeExist) {
                        $exportNode = new DataRoomExportNode();
                        $exportNode->setDataRoomExport($this->dataroom_export);
                        $exportNode->setDataRoom($dataroom);
                        $exportNode->setStatus(DataRoomExport::STATUS_000_NEW);
                        $exportNode->save();
                    }
                }
            }

            $exportNodeCollection = DataRoomExportNode::where('dataroom_export_id', $this->dataroom_export->id)->get();
            
            foreach ($exportNodeCollection as $exportNode) {
                if (!in_array($exportNode->status, [
                    DataRoomExportNode::STATUS_100_INPROGRESS,
                    DataRoomExportNode::STATUS_900_DONE,
                ])) {
                    DataRoomExportFetch::dispatch($exportNode, $propertyLocale);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
    }
}
