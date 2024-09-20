<?php

namespace App\Http\Classes\LogicalModels\Question;

use App\Http\Classes\Structure\CDateTime;
use App\Models\MySql\Biodeposit\User_questions;

class QuestionModel
{
    public function __construct(
        private User_questions $userQuestion,
    ){}

    public function sendQuestion(array $data): void
    {
        $this->userQuestion->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'question' => $data['question'],
            'answer' => null,
            'answered_at' => null,
            'created_at' => CDateTime::getCurrentDate(),
            'updated_at' => CDateTime::getCurrentDate(),
        ]);
    }
}
