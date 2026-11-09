<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // دریافت اخبار فعال همراه با اطلاعات سرویس متصل به هر خبر
    public function index()
    {
        $announcements = Announcement::with('service:id,title,price')
            ->where('is_active', true)
            ->latest()
            ->get();

        return response()->json($announcements);
    }

    // ثبت اطلاعیه یا خبر جدید توسط ادمین
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'service_id' => ['nullable', 'exists:services,id'],
            'is_active' => ['boolean'],
        ]);

        $announcement = Announcement::create($validated);

        return response()->json([
            'message' => 'اطلاعیه جدید با موفقیت منتشر شد.',
            'announcement' => $announcement->load('service:id,title,price'),
        ], 201);
    }

    // ویرایش اطلاعیه توسط ادمین
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'service_id' => ['nullable', 'exists:services,id'],
            'is_active' => ['boolean'],
        ]);

        $announcement->update($validated);

        return response()->json([
            'message' => 'اطلاعیه بروزرسانی شد.',
            'announcement' => $announcement->load('service:id,title,price'),
        ]);
    }

    // حذف اطلاعیه توسط ادمین
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return response()->json([
            'message' => 'اطلاعیه حذف شد.',
        ]);
    }
}