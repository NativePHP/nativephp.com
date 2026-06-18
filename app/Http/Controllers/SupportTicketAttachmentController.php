<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicket\Reply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SupportTicketAttachmentController extends Controller
{
    public function downloadTicketAttachment(SupportTicket $supportTicket, int $index): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user->id === $supportTicket->user_id || $user->isAdmin(), 403);

        $attachments = $supportTicket->attachments ?? [];

        abort_unless(isset($attachments[$index]), 404);

        $attachment = $attachments[$index];

        $url = Storage::disk('support-tickets')->temporaryUrl($attachment['path'], now()->addMinutes(5));

        return redirect($url);
    }

    public function downloadReplyAttachment(SupportTicket $supportTicket, Reply $reply, int $index): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user->id === $supportTicket->user_id || $user->isAdmin(), 403);
        abort_unless($reply->support_ticket_id === $supportTicket->id, 404);

        $attachments = $reply->attachments ?? [];

        abort_unless(isset($attachments[$index]), 404);

        $attachment = $attachments[$index];

        $url = Storage::disk('support-tickets')->temporaryUrl($attachment['path'], now()->addMinutes(5));

        return redirect($url);
    }
}
