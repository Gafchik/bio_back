<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Ви повинні прийняти :attribute.',
    'active_url' => 'Поле :attribute is not a valid URL.',
    'after' => 'Поле :attribute має бути датою після :date.',
    'after_or_equal' => 'Поле :attribute має бути датою після або рівною :date.',
    'alpha' => 'Поле :attribute може містити лише літери.',
    'alpha_dash' => 'Поле :attribute може містити лише літери, цифри, дефіс та підкреслення.',
    'alpha_num' => 'Поле :attribute може містити лише літери та цифри.',
    'array' => 'Поле :attribute має бути масивом.',
    'before' => 'Поле :attribute має бути датою перед :date.',
    'before_or_equal' => 'Поле :attribute має бути датою перед або рівною :date.',
    'between' => [
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'file' => 'Розмір :attribute повинен бути від :min до :max кілобайт.',
        'string' => 'Довжина :attribute повинна бути від :min до :max символів.',
        'array' => 'Поле :attribute повинно містити :min - :max елементів.',
    ],
    'boolean' => 'Поле :attribute має бути логічною істинною або брехнею.',
    'confirmed' => 'Поле :attribute не збігається з підтвердженням.',
    'date' => 'Поле :attribute не є датою.',
    'date_format' => 'Поле :attribute не відповідає формату :format.',
    'different' => 'Поля :attribute і :other повинні відрізнятися.',
    'digits' => 'Довжина цифрового поля :attribute повинна бути :digits.',
    'digits_between' => 'Довжина цифрового поля :attribute повинна бути між :min та :max.',
    'dimensions' => 'Поле :attribute має неприпустимі розміри зображення.',
    'distinct' => 'Поле :attribute має повторюване зачення.',
    'email' => 'Поле :attribute має бути дійсною електронною адресою.',
    'exists' => 'Вибране значення для :attribute неправильне.',
    'file' => 'Поле :attribute має бути файлом.',
    'filled' => "Поле :attribute обов'язкове для заповнення.",
     'image' => 'Поле :attribute має бути зображенням.',
     'in' => 'Вибране значення для :attribute помилкове.',
     'in_array' => 'Поле :attribute не існує в :other.',
     'integer' => 'Поле :attribute має бути цілим числом.',
     'ip' => 'Поле :attribute має бути дійсною IP-адресою.',
     'ipv4' => 'Поле :attribute має бути дійсною IPv4-адресою.',
     'ipv6' => 'Поле :attribute має бути дійсною IPv6-адресою.',
     'json' => 'Поле :attribute має бути валідним JSON рядком.',
     'max' => [
         'numeric' => 'Поле :attribute має бути не більше :max.',
         'file' => 'Поле :attribute має бути не більше :max Кілобайт.',
         'string' => 'Поле :attribute має бути не довшим за :max символів.',
         'array' => 'Поле :attribute повинно містити не більше :max елементів.',
     ],
     'mimes' => 'Поле :attribute має бути файлом одного з типів: :values.',
     'mimetypes' => 'Поле :attribute має бути файлом одного з типів: :values.',
     'min' => [
         'numeric' => 'Поле :attribute має бути не менше :min.',
         'file' => 'Поле :attribute має бути не менше :min Кілобайт.',
         'string' => 'Поле :attribute має бути не коротшим :min символів.',
         'array' => 'Поле :attribute повинно містити не менше :min елементів.'
     ],
     'not_in' => 'Вибране значення для :attribute помилкове.',
     'numeric' => 'Поле :attribute має бути числом.',
     'present' => 'Поле :attribute має бути присутнім.',
     'regex' => 'Поле :attribute має помилковий формат.',
     'required' => "Поле :attribute обов'язкове для заповнення.",
     'required_if' => "Поле :attribute обов'язкове для заповнення, коли :other одно :value.",
     'required_unless' => "Поле :attribute обов'язкове для заповнення, коли :other не дорівнює :values.",
     'required_with' => "Поле :attribute обов'язкове для заповнення, коли :values вказано.",
     'required_with_all' => "Поле :attribute обов'язкове для заповнення, коли :values вказано.",
     'required_without' => "Поле :attribute обов'язкове для заповнення, коли :values не вказано.",
     'required_without_all' => "Поле :attribute обов'язкове для заповнення, коли :values не вказано.",
     'same' => 'Значення :attribute повинно збігатися з :other.',
     'size' => [
    'numeric' => 'Поле :attribute має бути :size.',
    'file' => 'Поле :attribute має бути :size Кілобайт.',
    'string' => 'Поле :attribute має бути довжиною :size символів.',
    'array' => 'Кількість елементів у полі :attribute має бути :size.'
],
     'string' => 'Поле :attribute має бути рядком.',
     'timezone' => 'Поле :attribute має бути валідною тимчасовою зоною.',
     'unique' => 'Таке значення поля :attribute вже існує.',
     'uploaded' => 'Завантаження поля :attribute не вдалося.',
     'url' => 'Поле :attribute має помилковий формат.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-MailRabbit Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
