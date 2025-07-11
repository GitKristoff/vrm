<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pet $pet): bool
    {
        return $user->owner->id === $pet->owner_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->owner->id === $pet->owner_id;
    }

    public function delete(User $user, Pet $pet): bool
    {
        return $user->owner->id === $pet->owner_id;
    }
}
