<?php

use App\Models\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

if (!function_exists('user')) {
    /**
     * @return Authenticatable|User
     */
    function user()
    {
        return Auth::user() ? Auth::user() : new User();
    }
}
