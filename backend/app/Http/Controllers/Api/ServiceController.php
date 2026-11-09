<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // نمایش لیست خدمات فعال برای عموم کاربران
    public function index()
    {
        $services = Service::where('is_active', true)->latest()->get();
        return response()->json($services);
    }

    // نمایش جزئیات یک خدمت خاص
    public function show(Service $service)
    {
        return response()->json($service);
    }

    // افزودن خدمت جدید توسط ادمین
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'required_docs' => ['nullable', 'array'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $service = Service::create($validated);

        return response()->json([
            'message' => 'خدمت جدید با موفقیت ایجاد شد.',
            'service' => $service,
        ], 201);
    }

    // ویرایش خدمت توسط ادمین
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'required_docs' => ['nullable', 'array'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $service->update($validated);

        return response()->json([
            'message' => 'اطلاعات خدمت بروزرسانی شد.',
            'service' => $service,
        ]);
    }

    // حذف خدمت توسط ادمین
    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'message' => 'خدمت با موفقیت حذف شد.',
        ]);
    }
}