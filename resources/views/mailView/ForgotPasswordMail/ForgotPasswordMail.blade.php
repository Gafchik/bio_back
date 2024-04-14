<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .title {
            text-align: center;
        }
    </style>
</head>
<body>
<div>@lang($trans_prefix.'.text'){{$activationCode}}</div>
</body>
</html>
