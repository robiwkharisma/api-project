<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\Entities\Notification;
use App\Repositories\Entities\UserNotification;
use App\Repositories\Entities\Prospect;

class NotificationCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $resource;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message, $resource)
    {
        $this->message = $message;
        $this->resource = $resource;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (camel_case($this->message) == 'prospectActionReminder') {
                
                $prospect = Prospect::whereHas('prospectActivity', function ($query) {
                    $query->where('activity_type', 'phase');
                })->find($this->resource['typeId']);

                if (!empty($prospect)) return;
            }

            $notification = new Notification();
            $receiverIds = $notification->setResource($this->message, $this->resource);
            $notification->save();

            foreach ($receiverIds as $userId) {
                $pivot = new UserNotification();
                $pivot->setUser($userId);
                $pivot->setNotification($notification->id);
                $pivot->save();
            }   
        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
    }
}
