<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSupportController extends Controller
{
    /**
     * List tiket milik user.
     */
    public function index()
    {
        $user = Auth::user();

        $tickets = SupportTicket::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('support.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Form buat tiket baru.
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Simpan tiket baru + pesan pertama.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // ✅ VALIDASI: wajib ada category
        $data = $request->validate([
            'subject'  => ['required', 'string', 'max:191'],
            'category' => ['required', 'string', 'max:50'],
            'message'  => ['required', 'string'],
        ]);

        // ✅ SIMPAN TIKET + CATEGORY
        $ticket = SupportTicket::create([
            'user_id'  => $user->id,
            'subject'  => $data['subject'],
            'category' => $data['category'],   // <— ini yang tadi bikin error
            'status'   => 'open',
        ]);

        // Pesan pertama dari user
        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $user->id,
            'sender_type'       => 'user',
            'message'           => $data['message'],
        ]);

        return redirect()
            ->route('support.tickets.show', $ticket->id)
            ->with('status_support', 'Tiket support berhasil dibuat. Kami akan merespons secepatnya.');
    }

    /**
     * Detail tiket + percakapan.
     */
    public function show($id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $messages = $ticket->messages()
            ->orderBy('created_at')
            ->get();

        return view('support.show', [
            'ticket'   => $ticket,
            'messages' => $messages,
        ]);
    }

    /**
     * User membalas tiket.
     */
    public function reply(Request $request, $id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $user->id,
            'sender_type'       => 'user',
            'message'           => $data['message'],
        ]);

        // update status kalau tadinya closed bisa dibuka lagi, dll
        if ($ticket->status === 'closed') {
            $ticket->status = 'open';
        }
        $ticket->updated_at = now();
        $ticket->save();

        return back()->with('status_support', 'Pesan berhasil dikirim.');
    }

    /**
     * User menutup tiket.
     */
    public function close($id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $ticket->status = 'closed';
        $ticket->save();

        return back()->with('status_support', 'Tiket sudah ditandai selesai.');
    }
}
