<?php

namespace App\Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Functions\FunctionFile;

class AdminController
{
    public function index()
    {
        return view("Admin::pages.index");
    }

    public function download()
    {
        $message = FunctionFile::downloadFileFromTemplate('uploads/downloads/html/app-calendar.html');
        return $message['message'];
    }
    
}// End of class AdminController.php
