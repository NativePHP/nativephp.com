<?php

namespace App\Http\Controllers\Account\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Notifications\SupportTicketUserReplied;
use App\SupportTicket\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class TicketController extends Controller
{
    public static string $paginationLimit = '10';

    public function reply(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $this->authorize('reply', $supportTicket);

        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $reply = $supportTicket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->input('message'),
            'note' => false,
        ]);

        Notification::route('mail', 'support@nativephp.com')
            ->notify(new SupportTicketUserReplied($supportTicket, $reply));

        return redirect()
            ->route('support.tickets.show', $supportTicket)
            ->with('success', 'Your reply has been sent.');
    }

    public function closeTicket(SupportTicket $supportTicket): RedirectResponse
    {
        $this->authorize('closeTicket', $supportTicket);

        $supportTicket->update([
            'status' => Status::CLOSED,
        ]);

        return redirect()
            ->route('support.tickets.show', $supportTicket)
            ->with('success', __('account.support_ticket.close_ticket.success'));
    }

    public function index(): View
    {
        $supportTickets = SupportTicket::whereUserId(auth()->user()->id)
            ->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(static::$paginationLimit);

        return view('support.tickets.index', compact('supportTickets'));
    }

    public function show(SupportTicket $supportTicket): View
    {
        $this->authorize('view', $supportTicket);

        $supportTicket->load(['user', 'replies.user']);

        return view('support.tickets.show', compact('supportTicket'));
    }
}
