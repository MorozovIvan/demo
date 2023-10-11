<?php

namespace App\Services\GoogleCalendar;

use Assert\Assertion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ExpectedValues;
use App\Contracts\GoogleCalendar\GoogleCalendarServiceContract;
use Spatie\GoogleCalendar\Event;

/**
 * @deprecated
 *
 * Reason: this service is ok for one google account only
 */
class GoogleCalendarService implements GoogleCalendarServiceContract
{
    private Assertion $assert;

    private Event $event;

    /**
     * @param Event $event
     * @param Assertion $assert
     */
    public function __construct(Event $event, Assertion $assert)
    {
        $this->event = $event;
        $this->assert = $assert;
    }

    final public function getAllFutureEvents(): Collection
    {
        return $this->event::get();
    }

    final public function getEvent(int $eventId): Event
    {
        return $this->event::find($eventId);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    final public function createEvent(
        string $name,
        string $description,
        Carbon $startDateTime,
        Carbon $endDateTime,
        #[ExpectedValues(['email' => 'john@example.com', 'name' => 'John Doe', 'comment' => 'Lorum ipsum'])]
        array $attendees = []
    ): void {
        $this->assert::notEmpty($name, __('Event name is not specified'));
        $this->assert::notEmpty($description, __('Event description is not specified'));
        $this->assert::notEmpty($startDateTime, __('Event start date time is not specified'));
        $this->assert::notEmpty($endDateTime, __('Event end date time is not specified'));
        $this->assert::lessThan($startDateTime, $endDateTime);

        $event = $this->event;

        $event->name = $name;
        $event->description = $description;
        $event->startDateTime = $startDateTime;
        $event->endDateTime = $endDateTime;

        if (is_array(reset($attendees))) {
            foreach ($attendees as $attendee) {
                $event->addAttendee($attendee);
            }
        } else {
            $event->addAttendee($attendees);
        }

        $event->save();
    }

    #[ExpectedValues(['name' => 'Event name', 'description' => 'Some description', 'startDateTime' => 'Lorum ipsum'])]
    public function updateEvent(Event $event, array $fields = []): void
    {
        foreach ($fields as $field => $value) {
            $event->{$field} = $value;
        }

        $event->save();
    }

    public function deleteEvent(Event $event): void
    {
        $event->delete();
    }
}
