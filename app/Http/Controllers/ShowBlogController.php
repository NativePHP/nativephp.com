<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ShowBlogController extends Controller
{
    public function index()
    {
        return view('blog', [
            'articles' => Article::latest()->paginate(6),
        ]);
    }

    public function show(Article $article)
    {
        return view('article', [
            'article' => $article,
        ]);
    }
}
