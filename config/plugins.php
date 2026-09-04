<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Expansion Mode
    |--------------------------------------------------------------------------
    |
    | Controls what happens when a plugin version declares native permissions,
    | entitlements, capabilities, or background modes that its previous version
    | didn't have. See App\Enums\PermissionExpansionMode.
    |
    | "flag" — the new version is logged on the plugin's activity timeline and
    |          published immediately, same as today.
    | "gate" — the new version is withheld from being the plugin's current/
    |          visible version until an admin approves it in Filament.
    |
    */

    'permission_expansion_mode' => env('PLUGIN_PERMISSION_EXPANSION_MODE', 'flag'),

];
