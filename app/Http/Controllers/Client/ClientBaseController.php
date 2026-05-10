<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClientBaseController extends Controller
{
    protected function getClient()
    {
        return Auth::guard('client')->user()->client;
    }

    protected function getContact()
    {
        return Auth::guard('client')->user();
    }
}
