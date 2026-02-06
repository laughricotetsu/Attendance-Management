<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'COACHTECH 勤怠管理')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="header">
    <div class="header-inner">
        <img
            src="{{ asset('item/COACHTECHヘッダーロゴ.png') }}"
            alt="COACHTECH"
            class="header-logo"
        >

        <nav class="nav">
            <a href="/attendance">勤怠</a>
            <a href="/attendance/list">勤怠一覧</a>
            <a href="/stamp_correction_request/list">申請</a>
            <a href="#">ログアウト</a>
        </nav>
    </div>
</header>

<main class="main">
    @yield('content')
</main>

</body>
</html>
