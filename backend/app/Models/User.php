<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'national_code',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed', // لاراول به صورت خودکار پسورد را bcrypt می‌کند!
        ];
    }

    // یک کاربر (مشتری) می‌تواند چندین تیکت ثبت کند
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // متد کمکی برای چک کردن ادمین بودن کاربر
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}