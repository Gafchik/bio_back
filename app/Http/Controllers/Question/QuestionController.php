<?php

namespace App\Http\Controllers\Question;

use App\Http\Classes\LogicalModels\Question\Question;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends BaseController
{
    public function __construct(
        private Question $model
    )
    {
        parent::__construct();
    }
    public function sendQuestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'question' => ['required', 'string', 'max:500'],
        ]);
        $this->model->sendQuestion($validated);
        return $this->makeGoodResponse([]);
    }
}
