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
        abort_if($article->published_at->isFuture(), 404);

        return view('article', [
            'article' => $article,
        ]);
    }
}
