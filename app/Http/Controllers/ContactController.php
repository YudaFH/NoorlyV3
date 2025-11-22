<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        // Kirim email ke Gmail tujuan
        Mail::to(config('mail.contact_address'))
            ->send(new ContactMessageMail($validated));

        return back()->with('status', 'Pesan kamu sudah terkirim. Terima kasih sudah menghubungi Noorly! ✨');
    }
}
