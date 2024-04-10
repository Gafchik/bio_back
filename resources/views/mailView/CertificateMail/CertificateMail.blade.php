
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
        <h2 class="title">@lang($trans_prefix.'.title')</h2>
        <div>@lang($trans_prefix.'.row_1')</div>
        <div>@lang($trans_prefix.'.row_2')</div>
        <div>@lang($trans_prefix.'.row_3')</div>
        <div>@lang($trans_prefix.'.row_4')</div>
        <div>@lang($trans_prefix.'.row_5')</div>
        <div>@lang($trans_prefix.'.row_6')</div>
        <h3 class="title">@lang($trans_prefix.'.instruction')</h3>
        <ol>
            <li>@lang($trans_prefix.'.li_1')</li>
            <li>@lang($trans_prefix.'.li_2')</li>
            <li>@lang($trans_prefix.'.li_3')</li>
        </ol>
        <div>@lang($trans_prefix.'.footer')</div>
    </body>
</html>
