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
use Artisan;

class DuplicateDemoPortfolio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $demoCustomerId;
    protected $newCustomerId;
    protected $existingUserId;
    protected $demoPortfolioId;

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
    public function __construct($demoCustomerId, $newCustomerId, $existingUserId, $demoPortfolioId)
    {
        $this->demoCustomerId    = $demoCustomerId;
        $this->newCustomerId  = $newCustomerId;
        $this->existingUserId   = $existingUserId;
        $this->demoPortfolioId       = $demoPortfolioId;
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

            Artisan::call("myre:duplicate-customer-data", ['customerId' => $this->demoCustomerId, '--newCustomerId' => $this->newCustomerId, '--existingUserId' => $this->existingUserId, '--demoPortfolioId' => $this->demoPortfolioId]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
    }
}
