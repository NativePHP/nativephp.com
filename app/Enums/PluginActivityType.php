<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum PluginActivityType: string
{
    case Submitted = 'submitted';
    case Resubmitted = 'resubmitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case DescriptionUpdated = 'description_updated';
    case Withdrawn = 'withdrawn';
    case ReturnedToDraft = 'returned_to_draft';
    case MessageToDeveloper = 'message_to_developer';
    case MessageFromDeveloper = 'message_from_developer';
    case PermissionsExpanded = 'permissions_expanded';

    /**
     * Types that represent a message in the admin <-> developer conversation.
     *
     * @return array<int, self>
     */
    public static function messageTypes(): array
    {
        return [self::MessageToDeveloper, self::MessageFromDeveloper];
    }

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Submitted',
            self::Resubmitted => 'Resubmitted',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::DescriptionUpdated => 'Description Updated',
            self::Withdrawn => 'Withdrawn',
            self::ReturnedToDraft => 'Returned to Draft',
            self::MessageToDeveloper => 'Message Sent',
            self::MessageFromDeveloper => 'Developer Reply',
            self::PermissionsExpanded => 'Permissions Expanded',
        };
    }

    /**
     * The same history read from the developer's side of the conversation.
     */
    public function developerLabel(): string
    {
        return match ($this) {
            self::MessageToDeveloper => 'Message from NativePHP',
            self::MessageFromDeveloper => 'Your Reply',
            default => $this->label(),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Submitted => 'info',
            self::Resubmitted => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::DescriptionUpdated => 'gray',
            self::Withdrawn => 'warning',
            self::ReturnedToDraft => 'warning',
            self::MessageToDeveloper => 'primary',
            self::MessageFromDeveloper => 'info',
            self::PermissionsExpanded => 'warning',
        };
    }

    /**
     * Flux badge colour, grouping the log into approvals, rejections, each side
     * of the conversation, and muted everyday status changes.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Approved => 'green',
            self::Rejected => 'red',
            self::MessageToDeveloper => 'purple',
            self::MessageFromDeveloper => 'sky',
            self::PermissionsExpanded => 'amber',
            default => 'zinc',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Submitted => 'heroicon-o-paper-airplane',
            self::Resubmitted => 'heroicon-o-arrow-path',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::DescriptionUpdated => 'heroicon-o-pencil-square',
            self::Withdrawn => 'heroicon-o-arrow-uturn-left',
            self::ReturnedToDraft => 'heroicon-o-arrow-uturn-left',
            self::MessageToDeveloper => 'heroicon-o-chat-bubble-left-right',
            self::MessageFromDeveloper => 'heroicon-o-chat-bubble-left-ellipsis',
            self::PermissionsExpanded => 'heroicon-o-shield-exclamation',
        };
    }

    /**
     * The icon without its Blade component prefix, as Flux components expect it.
     */
    public function iconName(): string
    {
        return Str::after($this->icon(), 'heroicon-o-');
    }
}
