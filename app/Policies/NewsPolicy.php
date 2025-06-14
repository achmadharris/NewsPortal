<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, News $news)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isWartawan();
    }

    public function update(User $user, News $news)
    {
        return $user->isWartawan() && $user->id === $news->user_id;
    }

    public function delete(User $user, News $news)
    {
        return $user->isWartawan() && $user->id === $news->user_id;
    }

    public function restore(User $user, News $news)
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, News $news)
    {
        return $user->isAdmin();
    }
} 