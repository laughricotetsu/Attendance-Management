<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user_list.css') }}">
</head>
<body>

<header class="header">
    <div class="header-inner">
        {{-- ロゴ --}}
        <img src="{{ asset('item/COACHTECHヘッダーロゴ.png') }}" class="header-logo">

        {{-- ナビ --}}
        <nav class="nav">
            <a href="{{ route('attendance.index') }}">勤怠</a>
            <a href="{{ route('attendance.list') }}">勤怠一覧</a>
            <a href="#">申請</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button">ログアウト</button>
            </form>
        </nav>
    </div>
</header>

<main class="main">
    @yield('content')
</main>

</body>
</html>
