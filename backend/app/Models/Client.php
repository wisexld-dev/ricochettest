<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
