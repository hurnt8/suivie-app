<?php

namespace App\Policies;

use App\Models\Settings;
use App\Models\User;

class SettingsPolicy
{
    public function view(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Settings $settings): bool
    {
        return $user->isAdmin();
    }
}
