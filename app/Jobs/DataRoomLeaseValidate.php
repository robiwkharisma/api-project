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
use App\Repositories\Entities\User;
use App\Repositories\Contracts\DataRoomRepository;

use DB;
use Mail;

class DataRoomLeaseValidate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $leaseKeys;
    protected $portfolioId;
    protected $propertyId;
    protected $bailId;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param mixed $leaseKeys
     * @param integer $portfolioId
     * @param integer $propertyId
     * @param integer $bailId
     * @param integer $userId
     * @return void
     */
    public function __construct($leaseKeys, $portfolioId, $propertyId, $bailId, $userId)
    {
        $this->leaseKeys = $leaseKeys;
        $this->portfolioId = $portfolioId;
        $this->propertyId = $propertyId;
        $this->bailId = $bailId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DataRoomRepository $repositoryDataroom)
    {
       try {
            DB::beginTransaction();

            $user = User::findOrFail($this->userId);
            $repositoryDataroom->ignorePermission = true;
            $repositoryDataroom->propertyDataroomCheckAndGenerate($this->leaseKeys, $this->portfolioId, $this->propertyId, [$this->bailId]);
            $actionName = DataRoom::LEASE_VALIDATION;
            Mail::to($user->email)->queue(new \App\Mail\DataroomGenerateMailer($this->portfolioId,$this->propertyId, $this->bailId, $user, $actionName));
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
    }
}
