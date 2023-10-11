<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GoogleCalendar;

use App\Models\User;
use App\Models\Coach;
use Illuminate\Http\Request;
use App\Models\GoogleAccount;
use App\Http\Controllers\Api\ApiController;
use App\Services\Common\User\AccountService;
use App\Services\GoogleCalendar\GoogleService;
use function PHPUnit\Framework\isInstanceOf;

class GoogleAccountController extends ApiController
{
    /**
     * Display a listing of the google accounts.
     */
    public function index()
    {
        return response()->success($this->authUser()->googleAccounts);
    }

    /**
     * Handle the OAuth connection which leads to
     * the creating of a new Google Account.
     */
    public function store(Request $request, GoogleService $google, AccountService $accountService)
    {
        if (! $request->has('code')) {
            return response()->success(['auth_url' => $google->createAuthUrl()]);
        }

        $google->authenticate($request->get('code'));

        $token = $google->getAccessToken();

        $account = $google
            ->connectUsing($token)
            ->service('Oauth2')
            ->userinfo
            ->get();

        $user = $accountService->getUserByEmail($account->email);

        $accountData = array_merge(
            ['google_id' => $account->id],
            $user->isCoach() ? ['coach_id' => $user->id] : ['user_id' => $user->id]
        );

        $user->googleAccounts()->updateOrCreate(
            $accountData,
            [
                'name'  => (string) $account->name,
                'token' => $token,
            ]
        );

        return response()->success([], __('Account has been successfully stored'));
    }

    /**
     * Revoke the account's token and delete it locally.
     */
    public function destroy(GoogleAccount $googleAccount, GoogleService $google)
    {
        $googleAccount->calendars->each->delete();

        $googleAccount->delete();

        $google->revokeToken($googleAccount->token);

        return response()->success([], __('Account has been successfully removed'));
    }
}
