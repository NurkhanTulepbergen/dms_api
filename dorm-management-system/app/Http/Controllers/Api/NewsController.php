<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function __construct()
    {
        // Только админ и менеджер могут создавать, обновлять, удалять
        $this->middleware(['auth:sanctum', 'role:admin,manager'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Получить список новостей
     */
    public function index()
    {
        // Логирование новостей
        $news = News::latest()->get();
        \Log::info('News data:', $news->toArray()); // Логируем новости в файл

        // Ответ с новостями
        return response()->json([
            'status' => true,
            'news' => $news,
        ]);
    }

    /**
     * Добавить новость
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news = News::create($data);

        return response()->json(['message' => 'Новость добавлена', 'news' => $news], 201);
    }

    /**
     * Обновить новость
     */
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title'   => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image'   => 'nullable|image|max:2048',
        ]);

        if ($request->has('title')) $news->title = $request->title;
        if ($request->has('content')) $news->content = $request->content;

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $news->image = $request->file('image')->store('news', 'public');
        }

        $news->save();

        return response()->json(['message' => 'Новость обновлена', 'news' => $news]);
    }

    /**
     * Удалить новость
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return response()->json(['message' => 'Новость удалена']);
    }
}
