<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'service_interest' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $inquiry = Inquiry::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'service_interest' => $validated['service_interest'] ?? null,
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        // Format WhatsApp message redirect URL
        $rawWa = Setting::getByKey('whatsapp_number', '6281234567890');
        $waNumber = preg_replace('/[^0-9]/', '', $rawWa ?? '6281234567890');

        $textLines = [
            "Halo AP Consulting, saya berminat dengan layanan Anda. Berikut detail kontak saya:",
            "",
            "- *Nama*: {$inquiry->full_name}",
            "- *Email*: {$inquiry->email}",
            "- *Telepon*: " . ($inquiry->phone ?: '-'),
            "- *Perusahaan*: " . ($inquiry->company ?: '-'),
            "- *Layanan*: " . ($inquiry->service_interest ?: '-'),
            "- *Pesan*: {$inquiry->message}",
        ];

        $whatsappUrl = "https://wa.me/{$waNumber}?text=" . rawurlencode(implode("\n", $textLines));

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan kontak Anda berhasil dikirim.',
            'whatsapp_url' => $whatsappUrl,
            'data' => $inquiry,
        ], 201);
    }
}
