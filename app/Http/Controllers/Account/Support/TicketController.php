<?php

namespace App\Http\Controllers\Account\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\SupportTicket\Status;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public static string $paginationLimit = '10';

    public function closeTicket(SupportTicket $supportTicket)
    {
        $this->authorize('closeTicket', $supportTicket);

        $supportTicket->update([
            'status' => Status::CLOSED,
        ]);

        return redirect()
            ->route('support.tickets.show', $supportTicket)
            ->with('success', __('account.support_ticket.close_ticket.success'));
    }

    public function index()
    {
        $supportTickets = SupportTicket::whereUserId(auth()->user()->id)
            ->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(static::$paginationLimit);

        return view('support.tickets.index', compact('supportTickets'));
    }

    public function show(SupportTicket $supportTicket)
    {
        $this->authorize('view', $supportTicket);

        $supportTicket->load('user');

        return view('support.tickets.show', compact('supportTicket'));
    }
}
