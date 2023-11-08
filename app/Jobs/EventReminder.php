<?php

namespace App\Jobs;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use App\Notifications\EventComing;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EventReminder implements ShouldQueue
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
        $events = Event::whereDate('schedule_date', now()->addDays(1)->format('Y-m-d'))->get();

        foreach ($events as $key => $event) {
            foreach ($event->students as $key => $student) {
                $student->user->notify(new EventComing($event));
            }
        }
    }
}
