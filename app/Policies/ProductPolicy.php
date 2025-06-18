<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['user', 'admin', 'super_admin']);
    }

    public function view(User $user, Product $product)
    {
        return in_array($user->role, ['user', 'admin', 'super_admin']);
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function update(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function delete(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
