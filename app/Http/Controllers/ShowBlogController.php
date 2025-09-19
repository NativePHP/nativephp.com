<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Artesaos\SEOTools\Facades\SEOTools;

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

        // Set SEO metadata for the article
        SEOTools::setTitle($article->title . ' - Blog');
        SEOTools::setDescription($article->excerpt ?: 'Read this article on the NativePHP blog.');

        // Set OpenGraph metadata
        SEOTools::opengraph()->setTitle($article->title);
        SEOTools::opengraph()->setDescription($article->excerpt ?: 'Read this article on the NativePHP blog.');
        SEOTools::opengraph()->setType('article');

        // Set Twitter Card metadata
        SEOTools::twitter()->setTitle($article->title);
        SEOTools::twitter()->setDescription($article->excerpt ?: 'Read this article on the NativePHP blog.');

        return view('article', [
            'article' => $article,
        ]);
    }
}
