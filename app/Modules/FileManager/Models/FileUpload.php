<?php

namespace App\Modules\FileManager\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'file_uploads';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
