<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Example: show a list of user notifications
        return view('notifications.index');
    }
}
