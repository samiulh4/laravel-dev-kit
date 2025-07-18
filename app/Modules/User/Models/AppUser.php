<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($user) {
            $user->created_by = $user->id;
            $user->updated_by = $user->id;
            $user->saveQuietly(); // Use saveQuietly to avoid triggering another save event
        });
        static::saving(function ($user) {
            $user->updated_by = $user->id;
        });
    }
}
