<?php

namespace App\Jobs;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use App\Notifications\EventOver;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EventFinished implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $events = Event::whereDate('schedule_date', now()->format('Y-m-d'))
                        ->whereTime('schedule_hour', '<=', now()->subHours(6)->format('H:i:s'))
                        ->get();

        foreach ($events as $key => $event) {
            foreach ($event->students as $key => $student) {
                $student->user->notify(new EventOver($event));
            }
        }
    }
}
