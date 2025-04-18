<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsManagerController extends Controller
{
    public function index()
    {
        $newsList = News::orderBy('created_at', 'desc')->get();
        return view('manager.news.index', compact('newsList'));
    }

    public function create()
    {
        return view('manager.news.create');
    }

    public function store(Request $request)
    {
        // 1. Валидация
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

        return redirect()->route('manager.dashboard')
            ->with('successType', 'news_created')
            ->with('success', 'Новость создана!');
    }

    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('manager.dashboard', compact('news'));
    }


    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('manager.news.edit', compact('news')); // Заменили на правильное представление
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
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('news_images', 'public');
            $data['image'] = $path;
        }

        $news->update($data);

        return redirect()->route('manager.dashboard')->with('success', 'Новость обновлена!'); // Заменили на правильный маршрут
    }



    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Удалим картинку, если есть
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('manager.dashboard')->with('success', 'Новость удалена!');
    }
}
