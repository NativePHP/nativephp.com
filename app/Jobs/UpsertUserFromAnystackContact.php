<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpsertUserFromAnystackContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $maxExceptions = 1;

    public function __construct(
        public array $contactData
    ) {}

    public function handle(): void
    {
        $users = $this->matchingUsers();

        if ($users->count() > 1) {
            $userIds = $users->pluck('id')->implode(', ');

            throw new Exception("Multiple users [$userIds] found for contact by id [{$this->contactData['id']}] or email [{$this->contactData['email']}]");
        }

        $user = $users->first() ?? new User;

        $this->assertUserAttributesAreValid($user);

        Log::debug(($user->exists ? "Updating user [{$user->id}]" : 'Creating user')." from anystack contact [{$this->contactData['id']}].");

        $user->anystack_contact_id ??= $this->contactData['id'];
        $user->email ??= $this->contactData['email'];
        $user->name ??= $this->contactData['full_name'];
        $user->created_at ??= $this->contactData['created_at'];
        $user->updated_at ??= $this->contactData['updated_at'];
        $user->password ??= Hash::make(Str::random(72));

        $user->save();
    }

    protected function matchingUsers(): Collection
    {
        return User::query()
            ->where('email', $this->contactData['email'])
            ->orWhere('anystack_contact_id', $this->contactData['id'])
            ->get();
    }

    protected function assertUserAttributesAreValid(User $user): void
    {
        if (! $user->exists) {
            return;
        }

        if (filled($user->anystack_contact_id) && $user->anystack_contact_id !== $this->contactData['id']) {
            throw new Exception("User [{$user->id}] already exists but the user's anystack_contact_id [{$user->anystack_contact_id}] does not match the id [{$this->contactData['id']}].");
        }

        if ($user->email !== $this->contactData['email']) {
            throw new Exception("User [{$user->id}] already exists but the user's email [{$user->email}] does not match the email [{$this->contactData['email']}].");
        }
    }
}
