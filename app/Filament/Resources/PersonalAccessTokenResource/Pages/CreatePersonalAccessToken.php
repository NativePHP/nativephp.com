<?php

namespace App\Filament\Resources\PersonalAccessTokenResource\Pages;

use App\Filament\Resources\PersonalAccessTokenResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePersonalAccessToken extends CreateRecord
{
    protected static string $resource = PersonalAccessTokenResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // We need to handle token creation specially since we can't store plain text tokens
        /** @var User $user */
        $user = User::find($data['tokenable_id']);

        // Parse abilities - handle both string and array input
        $abilities = $data['abilities'] ?? '*';
        if (is_string($abilities)) {
            $abilities = $abilities === '*' ? ['*'] : explode(',', $abilities);
            $abilities = array_map('trim', $abilities);
        }

        $token = $user->createToken(
            name: $data['name'],
            abilities: $abilities,
            expiresAt: $data['expires_at'] ?? null
        );

        // Store the plain text token to show to user
        session(['new_api_token' => $token->plainTextToken]);

        // Return the token model
        return $token->accessToken;
    }

    protected function afterCreate(): void
    {
        $token = session('new_api_token');

        if ($token) {
            Notification::make()
                ->title('API Key Created Successfully')
                ->body("Your API key: {$token}")
                ->success()
                ->persistent()
                ->send();

            session()->forget('new_api_token');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
