<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display the editorial article and content freshness management queue.
     */
    public function index(): View
    {
        $articles = Article::with('category')
            ->orderBy('last_verified_at', 'asc')
            ->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new editorial guide.
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        return view('admin.articles.form', [
            'article' => new Article(),
            'categories' => $categories,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created editorial guide.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'category_id' => 'nullable|exists:categories,id',
            'author_name' => 'required|string|max:255',
            'author_title' => 'required|string|max:255',
            'reading_time_minutes' => 'required|integer|min:1|max:120',
            'excerpt' => 'required|string|max:1000',
            'content' => 'required|string',
            'sources_and_evidence' => 'nullable|string',
            'disclaimer' => 'nullable|string',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['last_verified_at'] = now();

        $article = Article::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('status', "Editorial guide '{$article->title}' successfully published.");
    }

    /**
     * Show the form for editing an editorial guide.
     */
    public function edit(Article $article): View
    {
        $categories = Category::active()->get();
        return view('admin.articles.form', [
            'article' => $article,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    /**
     * Update an editorial guide.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'category_id' => 'nullable|exists:categories,id',
            'author_name' => 'required|string|max:255',
            'author_title' => 'required|string|max:255',
            'reading_time_minutes' => 'required|integer|min:1|max:120',
            'excerpt' => 'required|string|max:1000',
            'content' => 'required|string',
            'sources_and_evidence' => 'nullable|string',
            'disclaimer' => 'nullable|string',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');

        if ($validated['is_published'] && !$article->published_at) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('status', "Editorial guide '{$article->title}' updated successfully.");
    }

    /**
     * Re-verify article content freshness.
     */
    public function markVerified(Article $article): RedirectResponse
    {
        $article->update([
            'last_verified_at' => now(),
        ]);

        return redirect()->back()
            ->with('status', "Content freshness re-verified for '{$article->title}'. Last verified timestamp updated to today.");
    }
}
