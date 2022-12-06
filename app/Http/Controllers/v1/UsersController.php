<?php

namespace App\Http\Controllers\v1;

use App\Models\User;
use Orion\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Fully-qualified model class name
     */
    protected $model = User::class;
}
