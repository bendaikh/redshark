<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    /**
     * Public self-registration is closed. Accounts are created by an admin.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Public self-registration is closed. Accounts are created by an admin.
     */
    public function store(Request $request): never
    {
        abort(404);
    }
}
