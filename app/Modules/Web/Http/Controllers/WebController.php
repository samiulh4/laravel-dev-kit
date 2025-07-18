<?php

namespace App\Modules\Web\Http\Controllers;

use Illuminate\Http\Request;

class WebController
{
    public function index()
    {
        return view("Web::pages.index");
    }
}
