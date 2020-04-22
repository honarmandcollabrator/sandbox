<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    سلام {{ $name }},
    <br>
    ممنون که در hsejob اکانت ایجاد کردید، فراموش نکنید که ثبت نام خود را کامل کنید!
    <br>
    لطفا روی لینک زیر کلیک کنید یا از ردیف آدرس، آن را کپی کنید و در سایت قرار دهید
    <br>

    <a href="{{ url('user/verify', $verification_code)}}">فعالسازی اکانت من</a>

    <br/>
</div>

</body>
</html>
