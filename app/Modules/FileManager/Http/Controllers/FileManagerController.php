<?php

namespace App\Modules\FileManager\Http\Controllers;

use Illuminate\Http\Request;

class FileManagerController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("FileManager::welcome");
    }
}
