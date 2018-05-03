<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Product;  
use App\Collection;  
use Carbon\Carbon;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // '\App\Console\PushPin'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $collection = Collection::where('status',1)
                                ->whereDate('next_time','<=', Carbon::now())
                                ->inRandomOrder()->first();
        $collectionId = $collection->collection_id;
        $schedule->command("push:pin $collectionId")
                 ->everyMinute()
                 ->appendOutputTo('output.log');
        $collection->next_time = Carbon::now()->addSecond($collection->timeout);
        $collection->save();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
