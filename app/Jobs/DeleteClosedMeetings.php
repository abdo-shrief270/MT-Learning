<?php

namespace App\Jobs;

use App\Mail\NewUserMail;
use App\Models\Meeting;
use App\Services\DailyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class DeleteClosedMeetings implements ShouldQueue
{
    use Queueable;
    public $dailyService;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->dailyService=new DailyService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $res=$this->dailyService->getMeetings();
        $grouped = array_reduce($res, function($result, $item) {
            $result[$item['room']][] = $item;
            return $result;
        }, []);

        foreach($grouped as $roomName=>$meetings)
        {
            if(!Meeting::where('name',$roomName)->count()>0){
                continue;
            }
            $delete=true;
            foreach ($meetings as $meeting){
                if($meeting['ongoing']){
                    $delete=false;
                }
            }
            if($delete){
                $this->dailyService->deleteRoom($roomName);
                Meeting::where('name', $roomName)->delete();
                Mail::to('abdo.shrief270@gmail.com')->send(new NewUserMail('Meeting: '.$roomName.' has been deleted'));

            }
        }

    }
}
