<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'tracking_code',
        'user_id',
        'service_id',
        'status',
        'delivery_method',
        'customer_note',
        'admin_note',
        'result_file_path',
    ];

    // این تیکت متعلق به کدام مشتری است؟
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // این تیکت برای کدام خدمت ثبت شده است؟
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // مدارک و فایل‌های پیوست‌شده توسط مشتری برای این تیکت
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}