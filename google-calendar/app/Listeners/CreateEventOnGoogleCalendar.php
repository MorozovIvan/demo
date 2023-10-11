<?php

namespace App\Listeners;

use App\Events\CalendarUpdated;
use App\Jobs\AddNewGoogleCalendarEvent;
use App\Http\Dto\GoogleCalendarEventDto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateEventOnGoogleCalendar implements ShouldQueue
{
    use InteractsWithQueue;

    protected ?Authenticatable $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(?Authenticatable $user)
    {
        $this->user = $user ?? auth()->user();
    }

    /**
     * Handle
     *
     * @param CalendarUpdated $event
     *
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function handle(CalendarUpdated $event)
    {
        $package = $event->event->package;

        $googleCalendarEventDto = new GoogleCalendarEventDto(
            summary: $package->name,
            description: $package->description,
            start: [
                'dateTime' => $event->event->iso_start_date,
                'timeZone' => $this->user->timezone,
            ],
            end: [
                'dateTime' => $event->event->iso_end_date,
                'timeZone' => $this->user->timezone,
            ],
        );

        if ($this->user->googleAccount && $this->user->googleAccount->token) {
            AddNewGoogleCalendarEvent::dispatch($googleCalendarEventDto, $this->user);
        }
    }
}
