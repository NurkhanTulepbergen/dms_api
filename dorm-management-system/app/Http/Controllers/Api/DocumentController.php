<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Предполагаем, что в таблице documents поле называется student_id и это по сути user_id
        $documents = Document::where('student_id', $user->id)->get();

        return response()->json($documents);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'documentFile' => 'required|file',
        ]);

        if (!$request->hasFile('documentFile')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Файл не выбран!',
            ], 400);
        }

        $file = $request->file('documentFile');

        $student_id = auth()->id();
        if (!$student_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка: студент не найден!',
            ], 401);
        }

        $document = (new Document())->upload($file, $student_id);

        if (!$document) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при сохранении документа в БД!',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Документ успешно загружен!',
            'document' => $document, // если нужно вернуть данные о документе
        ], 200);
    }


}
