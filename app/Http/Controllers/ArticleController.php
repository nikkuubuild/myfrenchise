<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    /**
     * Display the Knowledge Hub & Due Diligence Guide index.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()->get();
        $query = Article::published()->with('category')->recent();

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $articles = $query->paginate(9)->withQueryString();
        $selectedCategory = $request->input('category');

        return view('articles.index', compact('articles', 'categories', 'selectedCategory'));
    }

    /**
     * Display a comprehensive editorial guide with statutory citations and disclosures.
     */
    public function show(string $slug): View
    {
        $article = Article::where('slug', $slug)->with('category')->firstOrFail();

        if (! $article->is_published && ! auth()->check()) {
            abort(404, 'This editorial guide is not currently published.');
        }

        $schema = $this->seoService->articleSchema($article);

        return view('articles.show', compact('article', 'schema'));
    }
}
