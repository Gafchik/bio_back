<?php

namespace App\Http\Classes\LogicalModels\Question;

class Question
{
    public function __construct(
        private QuestionModel $model
    ){}

    public function sendQuestion(array $data): void
    {
        $this->model->sendQuestion($data);
    }
}
