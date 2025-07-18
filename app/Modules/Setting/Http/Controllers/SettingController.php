<?php

namespace App\Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;

class SettingController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("Setting::welcome");
    }
}
