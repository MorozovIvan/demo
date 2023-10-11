<?php

declare(strict_types=1);

namespace App\Http\Dto;

use JetBrains\PhpStorm\ExpectedValues;
use Spatie\DataTransferObject\DataTransferObject;

class GoogleCalendarEventDto extends DataTransferObject
{
    public string $summary;

    public string $location = '';

    public string $description = '';

    #[ExpectedValues(['dateTime' => '2021-09-28T09:00:00-07:00', 'timeZone' => 'America/Los_Angeles'])]
    public array $start;

    #[ExpectedValues(['dateTime' => '2021-09-28T17:00:00-07:00', 'timeZone' => 'America/Los_Angeles'])]
    public array $end;

    #[ExpectedValues(['RRULE:FREQ=DAILY;COUNT=2'])]
    public array $recurrence = [];

    #[ExpectedValues([['email' => 'lpage@example.com'], ['email' => 'sbrin@example.com']])]
    public array $attendees = [];

    #[ExpectedValues([
        'useDefault' => false,
        'overrides'  => [
            ['method' => 'email', 'minutes' => 24 * 60],
            ['method' => 'popup', 'minutes' => 10],
        ],
    ])]
    public array $reminders = [];
}
