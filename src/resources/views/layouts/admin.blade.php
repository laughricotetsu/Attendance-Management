<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'COACHTECH 勤怠管理（管理者）')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="header">
    <div class="header-inner">
        {{-- ロゴ --}}
        <img
            src="{{ asset('item/COACHTECHヘッダーロゴ.png') }}"
            alt="COACHTECH"
            class="header-logo"
        >

        {{-- 管理者ナビ --}}
        <nav class="nav">
            <a href="/admin/attendance/list">勤怠一覧</a>
            <a href="/admin/staff/list">スタッフ一覧</a>
            <a href="/stamp_correction_request/list">申請一覧</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button">
                    ログアウト
                </button>
            </form>

        </nav>
    </div>
</header>

<main class="main">
    @yield('content')
</main>

</body>
</html>
