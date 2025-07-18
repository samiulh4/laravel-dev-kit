<?php

namespace App\Modules\Cbr\Http\Controllers;

use Illuminate\Http\Request;

class CbrController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("Cbr::welcome");
    }
}
