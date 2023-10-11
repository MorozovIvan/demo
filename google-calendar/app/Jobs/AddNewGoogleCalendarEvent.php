<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Google_Service_Calendar_Event;
use InvalidArgumentException;
use Illuminate\Queue\SerializesModels;
use App\Http\Dto\GoogleCalendarEventDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\GoogleCalendar\GoogleService;
use Illuminate\Contracts\Auth\Authenticatable;

class AddNewGoogleCalendarEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected GoogleService $googleService;

    protected Authenticatable $user;

    protected GoogleCalendarEventDto $calendarEventDto;

    public function __construct(
        GoogleCalendarEventDto $calendarEventDto,
        Authenticatable $user,
    ) {
        $this->user = $user;
        $this->calendarEventDto = $calendarEventDto;
    }

    public function getGoogleCalendarService()
    {
        if (! $token = $this->user->googleAccount->token) {
            throw new InvalidArgumentException(__('There is no $token'));
        }

        return app(GoogleService::class)
            // We access the token through the `googleAccount` relationship.
            ->connectUsing($token)
            ->service('Calendar');
    }

    public function getCalendar()
    {
        // TODO: understand which of calendars we should use
        return $this->user->googleAccount->calendars()->first();
    }

    public function buildEvent(): Google_Service_Calendar_Event
    {
        return new Google_Service_Calendar_Event($this->calendarEventDto->toArray());
    }

    public function handle()
    {
        $event = $this->getGoogleCalendarService()
            ->events
            ->insert($this->getCalendar()->google_id, $this->buildEvent());

        return $event->htmlLink;
    }
}
