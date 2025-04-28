<?php

namespace App\SupportTicket;

enum Status: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case RESPONDED = 'responded';
    case CLOSED = 'closed';

    public function translated(): string
    {
        return __('account.support_ticket.status.' . $this->value);
    }
}
