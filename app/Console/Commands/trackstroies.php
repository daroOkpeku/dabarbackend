<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Models\Stories;
class trackstroies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:stories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $todaytime = Carbon::now('America/Los_Angeles');
        // Stories::whereIn('status', [0, '0'])->where('schedule_story_time', '<', $todaytime) ->update(['status' => 1]);
        Stories::whereIn('status', [0, '0'])->where('schedule_story_time', '<', $todaytime)->update(['status' => 1]);




    }
}
