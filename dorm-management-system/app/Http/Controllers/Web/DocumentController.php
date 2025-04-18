<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function index()
    {
        // Исправлено: ищем по `student_id`, а не `user_id`
        $documents = Document::where('student_id', auth()->id())->get();
        return view('student.documents', compact('documents'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Запрос на загрузку документа', ['user_id' => Auth::id()]);

            $request->validate([
                'documentFile' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            ]);

            if (!$request->hasFile('documentFile')) {
                return back()->with('error', 'Файл не выбран!');
            }

            $file = $request->file('documentFile');
            $path = $file->store('documents');

            // Записываем в базу
            $document = Document::create([
                'student_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
            ]);

            \Log::info('Документ загружен:', ['document' => $document]);

            if (!$document) {
                throw new \Exception('Не удалось сохранить документ в БД');
            }

            return redirect()->back()->with('success', 'Документ успешно загружен!');
        } catch (\Exception $e) {
            \Log::error('Ошибка при загрузке документа:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Ошибка загрузки: ' . $e->getMessage());
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'documentFile' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        if (!$request->hasFile('documentFile')) {
            return back()->with('error', 'Файл не выбран!');
        }

        $file = $request->file('documentFile');

        // Проверяем, есть ли `student_id`
        $student_id = auth()->id();
        if (!$student_id) {
            return back()->with('error', 'Ошибка: студент не найден!');
        }

        // Создаем документ через метод `upload()`
        $document = (new Document())->upload($file, $student_id);

        if (!$document) {
            return back()->with('error', 'Ошибка при сохранении документа в БД!');
        }

        return redirect()->back()->with('success', 'Документ успешно загружен!');
    }

}
