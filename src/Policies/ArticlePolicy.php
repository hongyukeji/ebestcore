<?php

namespace System\Policies;

use System\Models\Article;
use System\Models\User;

class ArticlePolicy
{
    public function update(?User $user, Article $article)
    {
        return $user->id === $article->user_id;
    }

    public function delete(?User $user, Article $article)
    {
        return $user->id === $article->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
