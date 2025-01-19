<?php

use App\Jobs\DeleteClosedMeetings;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DeleteClosedMeetings())->everyFiveMinutes();
