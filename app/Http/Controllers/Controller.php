<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function owner(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    protected function businessFor(Request $request): Business
    {
        return $this->owner($request)->business()->firstOrFail();
    }
}
