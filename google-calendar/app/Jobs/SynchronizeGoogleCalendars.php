<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\GoogleAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\GoogleCalendar\GoogleService;

class SynchronizeGoogleCalendars extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected GoogleAccount $googleAccount;

    public function __construct(GoogleAccount $googleAccount)
    {
        $this->googleAccount = $googleAccount;
    }

    public function getGoogleService()
    {
        return app(GoogleService::class)
            ->connectUsing($this->googleAccount->token)
            ->service('Calendar');
    }

    public function getGoogleRequest($service, $options)
    {
        return $service->calendarList->listCalendarList($options);
    }

    public function syncItem($googleCalendar)
    {
        if ($googleCalendar->deleted) {
            return $this->googleAccount->calendars()
                ->where('google_id', $googleCalendar->id)
                ->get()
                ->each
                ->delete();
        }

        $this->googleAccount->calendars()->updateOrCreate(
            [
                'google_id' => $googleCalendar->id,
                'google_account_id' => $this->googleAccount->id,
            ],
            [
                'name' => $googleCalendar->summary,
                'color' => $googleCalendar->backgroundColor,
                'timezone' => $googleCalendar->timeZone,
            ]
        );

        return null;
    }
}
