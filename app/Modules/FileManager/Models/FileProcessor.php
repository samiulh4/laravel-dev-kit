<?php

namespace App\Modules\FileManager\Models;

use Illuminate\Database\Eloquent\Model;

class FileProcessor extends Model
{
    protected $table = 'file_processors';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
