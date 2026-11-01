<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Announcement;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ۱. ساخت اکانت ادمین اصلی دفتر پیشخوان
        User::create([
            'name' => 'مدیر دفتر پیشخوان',
            'phone' => '09120000000',
            'national_code' => '0011223344',
            'role' => 'admin',
            'password' => '12345678', // به خاطر casts در مدل User، خودکار هش می‌شود
        ]);

        // ۲. ساخت یک مشتری تستی
        User::create([
            'name' => 'مشتری نمونه',
            'phone' => '09350000000',
            'national_code' => '1122334455',
            'role' => 'customer',
            'password' => '12345678',
        ]);

        // ۳. ثبت دو خدمت پرکاربرد پیشخوان
        $sejamService = Service::create([
            'title' => 'ثبت‌نام و احراز هویت سجام',
            'description' => 'ثبت‌نام کامل در سامانه سجام و دریافت کد بورسی بدون نیاز به مراجعه حضوری.',
            'required_docs' => ['تصویر روی کارت ملی', 'صفحه اول شناسنامه', 'شماره شبای بانکی'], // خودکار به JSON تبدیل می‌شود
            'price' => 150000,
            'is_active' => true,
        ]);

        $carService = Service::create([
            'title' => 'ثبت‌نام خودرو (ایران‌خودرو و سایپا)',
            'description' => 'وکالتی کردن حساب و ثبت درخواست در طرح‌های فروش فوق‌العاده و پیش‌فروش خودرو.',
            'required_docs' => ['تصویر کارت ملی', 'تصویر گواهینامه رانندگی'],
            'price' => 200000,
            'is_active' => true,
        ]);

        // ۴. ثبت یک خبر جدید و اتصال مستقیم آن به خدمت ثبت‌نام خودرو
        Announcement::create([
            'title' => 'آغاز دور جدید ثبت‌نام ایران‌خودرو (طرح عادی و مادران)',
            'content' => 'مهلت وکالتی کردن حساب تا پایان هفته جاری است. برای ثبت‌نام سریع بدون مراجعه حضوری، مدارک خود را از طریق دکمه زیر ارسال کنید.',
            'service_id' => $carService->id, // وصل کردن خبر به سرویس خودرو
            'is_active' => true,
        ]);
    }
}