<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    // لیست تیکت‌ها (ادمین همه را می‌بیند، مشتری فقط تیکت‌های خودش را)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $tickets = Ticket::with(['user:id,name,phone,national_code', 'service:id,title,price', 'attachments'])
                ->latest()
                ->get();
        } else {
            $tickets = $user->tickets()
                ->with(['service:id,title,price', 'attachments'])
                ->latest()
                ->get();
        }

        return response()->json($tickets);
    }

    // ثبت تیکت جدید توسط مشتری به همراه آپلود مدارک
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'delivery_method' => ['required', 'in:online,in_person'],
            'customer_note' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.title' => ['nullable', 'string', 'max:255'],
            'attachments.*.file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // حداکثر ۵ مگابایت
        ]);

        // استفاده از تراکنش دیتابیس برای اطمینان از ثبت همزمان تیکت و مدارک
        $ticket = DB::transaction(function () use ($request, $validated) {
            $newTicket = Ticket::create([
                'tracking_code' => 'PK-' . strtoupper(Str::random(6)),
                'user_id' => $request->user()->id,
                'service_id' => $validated['service_id'],
                'delivery_method' => $validated['delivery_method'],
                'customer_note' => $validated['customer_note'] ?? null,
                'status' => 'pending',
            ]);

            if ($request->has('attachments')) {
                foreach ($request->attachments as $item) {
                    $path = $item['file']->store('tickets/attachments', 'public');

                    $newTicket->attachments()->create([
                        'title' => $item['title'] ?? 'مدرک پیوست',
                        'file_path' => $path,
                    ]);
                }
            }

            return $newTicket;
        });

        return response()->json([
            'message' => 'درخواست شما با موفقیت ثبت شد.',
            'ticket' => $ticket->load(['service:id,title', 'attachments']),
        ], 201);
    }

    // مشاهده جزئیات یک تیکت خاص
    public function show(Request $request, Ticket $ticket)
    {
        if (! $request->user()->isAdmin() && $ticket->user_id !== $request->user()->id) {
            return response()->json(['message' => 'عدم دسترسی به این تیکت.'], 403);
        }

        return response()->json(
            $ticket->load(['user:id,name,phone,national_code', 'service:id,title,price', 'attachments'])
        );
    }

    // بررسی تیکت، تغییر وضعیت و آپلود فایل نتیجه نهایی توسط ادمین
    public function manage(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,contacted,in_progress,completed,rejected'],
            'admin_note' => ['nullable', 'string'],
            'result_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,zip', 'max:10240'], // حداکثر ۱۰ مگابایت
        ]);

        if ($request->hasFile('result_file')) {
            $ticket->result_file_path = $request->file('result_file')->store('tickets/results', 'public');
        }

        $ticket->status = $validated['status'];
        if (array_key_exists('admin_note', $validated)) {
            $ticket->admin_note = $validated['admin_note'];
        }

        $ticket->save();

        return response()->json([
            'message' => 'وضعیت تیکت با موفقیت بروزرسانی شد.',
            'ticket' => $ticket->load(['user:id,name,phone', 'service:id,title', 'attachments']),
        ]);
    }
}