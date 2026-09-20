<?php

namespace App\Policies;

use App\Models\Sender;
use App\Models\User;

class SenderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Sender $sender): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Sender $sender): bool
    {
        return true;
    }

    public function delete(User $user, Sender $sender): bool
    {
        return $user->isAdmin();
    }
}
