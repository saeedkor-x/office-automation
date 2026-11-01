<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'service_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // هر خبر می‌تواند به یک سرویس خاص اشاره کند (اختیاری)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}