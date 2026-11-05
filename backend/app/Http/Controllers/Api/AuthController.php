<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ثبت‌نام مشتری جدید
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'size:11', 'unique:users,phone'],
            'national_code' => ['nullable', 'string', 'size:10', 'unique:users,national_code'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'national_code' => $validated['national_code'] ?? null,
            'role' => 'customer', // هر کاربری که از سایت ثبت‌نام کند به صورت پیش‌فرض مشتری است
            'password' => $validated['password'],
        ]);

        $token = $user->createToken('pishkhan_token')->plainTextToken;

        return response()->json([
            'message' => 'ثبت‌نام با موفقیت انجام شد.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // ورود کاربر (مشتری یا ادمین) با شماره موبایل
    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'size:11'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'شماره موبایل یا رمز عبور اشتباه است.',
            ], 401);
        }

        $token = $user->createToken('pishkhan_token')->plainTextToken;

        return response()->json([
            'message' => 'ورود موفقیت‌آمیز بود.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // دریافت اطلاعات کاربر لاگین‌شده برای فرانت‌اند
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // خروج و باطل کردن توکن فعلی
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'با موفقیت از حساب خارج شدید.',
        ]);
    }
}