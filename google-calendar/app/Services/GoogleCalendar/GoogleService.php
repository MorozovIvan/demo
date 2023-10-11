<?php

declare(strict_types=1);

namespace App\Services\GoogleCalendar;

use Google_Client;
use Illuminate\Config\Repository;

class GoogleService
{
    private Google_Client $client;

    public function __construct(array $config)
    {
        $client = new Google_Client();

        $client->setClientId($config['client_id']);
        $client->setClientSecret($config['client_secret']);
        $client->setRedirectUri($config['redirect_uri']);
        $client->setScopes($config['scopes']);
        $client->setApprovalPrompt($config['approval_prompt']);
        $client->setAccessType($config['access_type']);
        $client->setIncludeGrantedScopes($config['include_granted_scopes']);

        $this->client = $client;
    }

    final public function connectUsing(string|array $token): static
    {
        $this->client->setAccessToken($token);

        return $this;
    }

    final public function revokeToken(string|array|null $token = null): bool
    {
        $token = $token ?? $this->client->getAccessToken();

        return $this->client->revokeToken($token);
    }

    final public function service(string $service)
    {
        $classname = "Google_Service_$service";

        return new $classname($this->client);
    }

    /**
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if (! method_exists($this->client, $method)) {
            throw new \Exception("Call to undefined method '{$method}'");
        }

        return call_user_func_array([$this->client, $method], $args);
    }
}
