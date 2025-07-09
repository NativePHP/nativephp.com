<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ShowBlogController extends Controller
{
    public function index()
    {
        $articles = Article::query()
            ->published()
            ->paginate(6);

        return view('blog', [
            'articles' => $articles,
        ]);
    }

    public function show(Article $article)
    {
        abort_unless($article->isPublished() || auth()->user()?->isAdmin(), 404);

        return view('article', [
            'article' => $article,
        ]);
    }
}
