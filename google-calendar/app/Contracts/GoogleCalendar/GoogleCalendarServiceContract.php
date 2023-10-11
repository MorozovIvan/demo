<?php

declare(strict_types=1);

namespace App\Contracts\GoogleCalendar;

use Illuminate\Support\Carbon;
use Spatie\GoogleCalendar\Event;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ExpectedValues;

interface GoogleCalendarServiceContract
{
    public function getAllFutureEvents(): Collection;

    public function getEvent(int $eventId): Event;

    public function createEvent(
        string $name,
        string $description,
        Carbon $startDateTime,
        Carbon $endDateTime,
        #[ExpectedValues(['email' => 'john@example.com', 'name' => 'John Doe', 'comment' => 'Lorum ipsum'])]
        array $attendees = []
    ): void;

    #[ExpectedValues(['name' => 'Event name', 'description' => 'Some description', 'startDateTime' => 'Lorum ipsum'])]
    public function updateEvent(Event $event, array $fields = []): void;

    public function deleteEvent(Event $event): void;
}
