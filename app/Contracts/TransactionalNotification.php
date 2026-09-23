<?php

namespace App\Contracts;

use App\Listeners\SuppressMailNotificationListener;

/**
 * Marker interface for notifications that must always be delivered,
 * regardless of the user's email preferences or verification state.
 *
 * Account recovery, purchase receipts, and license/entitlement
 * notifications fall into this category.
 *
 * @see SuppressMailNotificationListener
 */
interface TransactionalNotification {}
