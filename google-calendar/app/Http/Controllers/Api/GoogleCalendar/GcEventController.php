<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GoogleCalendar;

use App\Http\Controllers\Api\ApiController;

class GcEventController extends ApiController
{
    public function index()
    {
        $events = $this->authUser()
            ->gcEvents()
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->success($events);
    }
}
