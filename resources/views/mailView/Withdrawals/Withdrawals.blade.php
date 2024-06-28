<!DOCTYPE html>
<html lang="ru">
<head>
    <title>BioDeposit</title>
    <!--meta-->
    <meta charset="utf-8">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:title" content="">
    <meta property="og:site_name" content="">
    <meta property="og:image" content="img/sharing.jpg">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta property="og:description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body style="min-width:320px;margin: 0;padding: 15px;">
<div class="container" style="padding: 15px 30px 70px;;height: 100%;max-width: 600px;margin:0 auto;background-image: url({!! url('/img/mail-back.png') !!});background-repeat: no-repeat;background-position: 50% 0;background-size: 102% 103%;">
    <div class="wrapper" style="max-width: 600px;height: 100%; padding:20px;">
        <div>
            Запрос на вывод средств от {{$full_name}}. На сумму: {{$amount}}$
        </div>
        <br>
        <div>
            Withdrawal request from {{$full_name}}. Amount: {{$amount}}$
        </div>
    </div>
</div>
</body>
</html>
