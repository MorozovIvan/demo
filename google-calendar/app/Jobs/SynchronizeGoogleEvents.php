<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\GoogleCalendar\GoogleService;

class SynchronizeGoogleEvents extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Calendar $calendar;

    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    public function getGoogleService()
    {
        return app(GoogleService::class)
            // We access the token through the `googleAccount` relationship.
            ->connectUsing($this->calendar->googleAccount->token)
            ->service('Calendar');
    }

    public function getGoogleRequest($service, $options)
    {
        return $service->events->listEvents(
        // We provide the Google ID of the calendar from which we want the events.
            $this->calendar->google_id, $options
        );
    }

    public function syncItem($googleEvent)
    {
        // A Google event has been deleted if its status is `cancelled`.
        if ($googleEvent->status === 'cancelled') {
            return $this->calendar->gcEvents()
                ->where('google_id', $googleEvent->id)
                ->delete();
        }

        $this->calendar->gcEvents()->updateOrCreate(
            [
                'google_id' => $googleEvent->id,
            ],
            [
                'name'        => $googleEvent->summary,
                'description' => $googleEvent->description,
                'all_day'     => $this->isAllDayEvent($googleEvent),
                'started_at'  => $this->parseDatetime($googleEvent->start),
                'ended_at'    => $this->parseDatetime($googleEvent->end),
            ]
        );

        return null;
    }

    protected function isAllDayEvent($googleEvent)
    {
        return ! $googleEvent->start->dateTime && ! $googleEvent->end->dateTime;
    }

    protected function parseDatetime($googleDatetime)
    {
        $rawDatetime = $googleDatetime->dateTime ?: $googleDatetime->date;

        return Carbon::parse($rawDatetime)->setTimezone('UTC');
    }
}
