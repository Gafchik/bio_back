<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
<h2 class="title">@lang($trans_prefix.'.subject')</h2>
<div>
    <span class="text-bold">@lang($trans_prefix.'.name')</span>
    <span>{{$swift['name']}}</span>
</div>
<div>
    <span class="text-bold">@lang($trans_prefix.'.company_name')</span>
    <span>{{$swift['company_name']}}</span>
</div>
<div>
    <span class="text-bold">@lang($trans_prefix.'.address')</span>
    <span>{{$swift['address']}}</span>
</div>
<div>
    <span class="text-bold">@lang($trans_prefix.'.phone')</span>
    <span>{{$swift['phone']}}</span>
</div>
<div>
    <span class="text-bold">@lang($trans_prefix.'.top_up_amount')</span>
    <span>{{$top_up_amount}}$</span>
</div>
</body>
</html>
