<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 👉 Kapcsolat a Role modellel
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // 👉 Kapcsolat a Teacher modellel
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    // 👉 Kapcsolat a Student modellel
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}