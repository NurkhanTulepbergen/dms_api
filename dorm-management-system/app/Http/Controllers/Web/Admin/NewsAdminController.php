<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsAdminController extends Controller
{

    public function index()
    {
        $newsList = News::orderBy('created_at', 'desc')->get();
        return view('admin.news.index', compact('newsList'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news_images', 'public');
            $data['image'] = $path;
        }

        News::create($data);

        return redirect()->route('admin.dashboard')
            ->with('successType', 'news_created')
            ->with('success', 'Новость создана!');
    }

    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('admin.dashboard', compact('news'));
    }


    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.dashboard', compact('news'));
    }


    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            // Удалим старую картинку (если нужна такая логика)
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('news_images', 'public');
            $data['image'] = $path;
        }

        $news->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Новость обновлена!');
    }


    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Удалим картинку, если есть
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Новость удалена!');
    }
}
