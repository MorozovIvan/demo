<?php

namespace App\Providers;

use Assert\Assertion;
use Illuminate\Support\Carbon;
use Spatie\GoogleCalendar\Event;
use App\Services\Zoom\ZoomService;
use App\Services\GoogleCalendar\GoogleService;
use App\Contracts\Admin\BlogServiceContract;
use App\Services\Common\User\AccountService;
use Illuminate\Filesystem\FilesystemManager;
use App\Services\Common\Files\fileUploaderService;
use App\Contracts\Common\User\AccountServiceContract;
use App\Services\GoogleCalendar\GoogleCalendarService;
use App\Contracts\GoogleCalendar\GoogleCalendarServiceContract;
use App\Contracts\Common\Services\Files\fileUploaderServiceContract;
use App\Contracts\Client\Services\Authentication\AuthenticationServiceContract;
use App\Contracts\Common\Migration\SeedCallerServiceContract;
use App\Contracts\Common\Services\Code\CodeContract;
use App\Contracts\Paypal\PaypalServiceContract;
use App\Contracts\Zoom\ZoomServiceContract;
use App\Contracts\Common\Services\Timezone\TimezoneServiceContract;
use App\Services\Admin\Blog\BlogService;
use App\Services\Client\Authentication\AuthenticationService;
use App\Services\Common\Code\CodeService;
use App\Services\Common\Migration\SeedCallerService;
use App\Services\Paypal\PaypalService;
use App\Services\Common\Timezone\TimezoneService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Contracts\Coach\Services\Authentication\AuthenticationServiceContract as CoachAuthenticationServiceContract;
use App\Services\Coach\Authentication\AuthenticationService as CoachAuthenticationService;

class BindServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GoogleService::class, function ($app) {
            return new GoogleService(
                config('services.google')
            );
        });
    }
}
