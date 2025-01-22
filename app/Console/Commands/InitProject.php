<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Psy\Readline\Hoa\Console;

class InitProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('migrate:fresh --seed');
        Artisan::call('shield:generate --all --panel=admin');
        Artisan::call('shield:super-admin --user=1');
    }
}
