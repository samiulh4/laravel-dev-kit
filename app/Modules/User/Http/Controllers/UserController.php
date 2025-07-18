<?php

namespace App\Modules\User\Http\Controllers;

use Illuminate\Http\Request;

class UserController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("User::welcome");
    }
}
