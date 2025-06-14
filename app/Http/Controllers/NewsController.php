<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:wartawan')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('role:editor')->only(['approve', 'reject']);
    }

    public function index()
    {
        $news = auth()->user()->isWartawan() 
            ? auth()->user()->news()->with('category')->latest()->paginate(10)
            : News::with(['category', 'author'])->latest()->paginate(10);
            
        return view('news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news-images', 'public');
            $validated['image'] = $path;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';

        News::create($validated);

        return redirect()->route('news.index')
            ->with('success', 'News article created successfully and waiting for approval.');
    }

    public function edit(News $news)
    {
        $this->authorize('update', $news);
        $categories = Category::all();
        return view('news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $this->authorize('update', $news);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('news-images', 'public');
            $validated['image'] = $path;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $news->update($validated);

        return redirect()->route('news.index')
            ->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $this->authorize('delete', $news);
        
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        
        $news->delete();

        return redirect()->route('news.index')
            ->with('success', 'News article deleted successfully.');
    }

    public function approve(News $news)
    {
        $news->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'published_at' => now()
        ]);

        return redirect()->route('news.index')
            ->with('success', 'News article approved and published.');
    }

    public function reject(News $news)
    {
        $news->update([
            'status' => 'rejected',
            'approved_by' => auth()->id()
        ]);

        return redirect()->route('news.index')
            ->with('success', 'News article rejected.');
    }
} 