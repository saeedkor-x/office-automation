<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
                Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 20)->unique(); // کد رهگیری یکتا برای مشتری
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // مشتری درخواست‌دهنده
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete(); // خدمت انتخاب‌شده
            $table->enum('status', ['pending', 'contacted', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->enum('delivery_method', ['online', 'in_person'])->default('online'); // نحوه تحویل: آنلاین یا حضوری
            $table->text('customer_note')->nullable(); // توضیحات مشتری موقع ثبت
            $table->text('admin_note')->nullable(); // یادداشت یا پاسخ ادمین
            $table->string('result_file_path')->nullable(); // فایل نتیجه نهایی که ادمین برای مشتری آپلود می‌کند
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
