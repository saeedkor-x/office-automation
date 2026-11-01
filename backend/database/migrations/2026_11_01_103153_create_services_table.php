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
            Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // مثلاً: ثبت‌نام سجام، تعویض پلاک
        $table->text('description')->nullable(); // توضیحات خدمت
        $table->json('required_docs')->nullable(); // لیست مدارک لازم به صورت آرایه JSON (مثلاً ["کارت ملی", "شناسنامه"])
        $table->unsignedBigInteger('price')->default(0); // هزینه خدمت (به تومان/ریال)
        $table->boolean('is_active')->default(true); // فعال یا غیرفعال بودن خدمت
        $table->timestamps();
            }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
