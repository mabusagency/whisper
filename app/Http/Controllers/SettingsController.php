<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends AppController
{

    public function profile()
    {
        return view('settings');
    }

    public function security()
    {
        return view('security');
    }
}
