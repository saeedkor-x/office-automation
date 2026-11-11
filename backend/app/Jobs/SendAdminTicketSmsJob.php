<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendAdminTicketSmsJob implements ShouldQueue
{
    use Queueable;

    // تعداد دفعات تلاش مجدد در صورت قطع بودن پنل پیامکی
    public int $tries = 3;

    public function __construct(public Ticket $ticket)
    {
    }

    public function handle(): void
    {
        // بارگذاری اطلاعات مشتری و خدمت مربوط به تیکت
        $this->ticket->loadMissing(['user', 'service']);

        // پیدا کردن شماره موبایل ادمین‌ها
        $adminPhones = User::where('role', 'admin')->pluck('phone');

        $message = "دفتر پیشخوان: درخواست جدید برای خدمت «{$this->ticket->service->title}» توسط {$this->ticket->user->name} ({$this->ticket->user->phone}) با کد رهگیری {$this->ticket->tracking_code} ثبت شد.";

        foreach ($adminPhones as $phone) {
            // ثبت در فایل لاگ برای تست در محیط توسعه (بدون نیاز به خرید پنل پیامک)
            Log::info("SMS Sent to Admin [{$phone}]: {$message}");

            /*
             * نمونه کد واقعی اتصال به وب‌سرویس پیامک (مثلاً کاوه‌نگار یا SMS.ir) در زمان انتشار:
             * Http::post('https://api.kavenegar.com/v1/' . env('SMS_API_KEY') . '/sms/send.json', [
             *     'receptor' => $phone,
             *     'message' => $message,
             * ]);
             */
        }
    }
}