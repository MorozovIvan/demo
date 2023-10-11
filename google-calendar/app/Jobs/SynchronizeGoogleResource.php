<?php

declare(strict_types=1);

namespace App\Jobs;

abstract class SynchronizeGoogleResource
{
    public function handle()
    {
        // Start with an empty page token.
        $pageToken = null;

        // Delegate service instantiation to the sub class.
        $service = $this->getGoogleService();

        do {
            // Ask the sub class to perform an API call with this pageToken (initially null).
            $list = $this->getGoogleRequest($service, compact('pageToken'));

            foreach ($list->getItems() as $item) {
                // The sub class is responsible for mapping the data into our database.
                $this->syncItem($item);
            }

            // Get the new page token from the response.
            $pageToken = $list->getNextPageToken();

            // Continue until the new page token is null.
        } while ($pageToken);
    }

    abstract public function getGoogleService();
    abstract public function getGoogleRequest($service, $options);
    abstract public function syncItem($item);
}
