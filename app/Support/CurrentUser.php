<?php

namespace App\Support;

use App\Models\User;

/**
 * Stand-in for the signed-in user until authentication is built.
 * Replace the body with `auth()->user()` in the authentication phase.
 */
class CurrentUser
{
    public static function get(): User
    {
        return User::query()->orderBy('id')->firstOrFail();
    }
}
