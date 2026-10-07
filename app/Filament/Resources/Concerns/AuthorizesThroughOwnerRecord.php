<?php

namespace App\Filament\Resources\Concerns;

use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * For relation managers whose records have no policy of their own (prices,
 * activity, replies...). Seeing them follows the owner's "view" permission
 * and changing them follows the owner's "update" permission.
 */
trait AuthorizesThroughOwnerRecord
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return static::shouldSkipAuthorization()
            || Gate::forUser(Filament::auth()->user())->allows('view', $ownerRecord);
    }

    public function getAuthorizationResponse(string $action, ?Model $record = null): Response
    {
        if (static::shouldSkipAuthorization()) {
            return Response::allow();
        }

        $ability = in_array($action, ['viewAny', 'view'], true) ? 'view' : static::ownerUpdateAbility();

        return Gate::forUser(Filament::auth()->user())->inspect($ability, $this->getOwnerRecord());
    }

    /**
     * The owner ability needed to change these records.
     */
    protected static function ownerUpdateAbility(): string
    {
        return 'update';
    }
}
