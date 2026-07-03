<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\BlogFeed;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Response;

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

    public function feed(BlogFeed $feed): Response
    {
        $articles = Article::query()
            ->published()
            ->with('author')
            ->limit(20)
            ->get();

        return response($feed->toRss($articles), 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }

    public function show(Article $article)
    {
        abort_unless($article->isPublished() || $article->isScheduled() || auth()->user()?->isAdmin(), 404);

        // Set SEO metadata for the article
        SEOTools::setTitle($article->title.' - Blog');
        SEOTools::setDescription($article->excerpt ?: 'Read this article on the NativePHP blog.');

        // Set OpenGraph metadata
        SEOTools::opengraph()->setTitle($article->title);
        SEOTools::opengraph()->setDescription($article->excerpt ?: 'Read this article on the NativePHP blog.');
        SEOTools::opengraph()->setType('article');

        if ($article->og_image) {
            SEOTools::opengraph()->addImage($article->og_image);
        }

        // Set Twitter Card metadata
        SEOTools::twitter()->setTitle($article->title);
        SEOTools::twitter()->setDescription($article->excerpt ?: 'Read this article on the NativePHP blog.');

        if ($article->og_image) {
            SEOTools::twitter()->setImage($article->og_image);
        }

        return view('article', [
            'article' => $article,
        ]);
    }
}
