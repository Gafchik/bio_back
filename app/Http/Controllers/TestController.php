<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TestController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function test(): JsonResponse
    {
        return $this->makeGoodResponse([]);
//        $imagePath = 'public/ru_1.png';
////        $files = Storage::allFiles();
////        dd($files);
//        // Проверяем, существует ли файл
//        if (Storage::exists($imagePath)) {
//            // Генерируем URL-адрес для изображения
//            $imageUrl = Storage::url($imagePath);
//            // Возвращаем URL-адрес в виде JSON-ответа
//            return $this->makeGoodResponse(['image_url' => $imageUrl]);
//        } else {
//            // Если файл не найден, возвращаем ошибку 404
//            return response()->json(['error' => 'Image not found'], 404);
//        }

    }

}
