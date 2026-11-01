<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'description',
        'required_docs',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_docs' => 'array', // تبدیل خودکار JSON دیتابیس به آرایه
            'is_active' => 'boolean',
        ];
    }

    // هر سرویس می‌تواند در چندین تیکت درخواست شده باشد
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // هر سرویس می‌تواند به چندین خبر/اطلاعیه متصل باشد
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}