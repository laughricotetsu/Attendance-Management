<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'COACHTECH 勤怠管理')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    {{-- ヘッダー（ログイン前） --}}
    <header style="background-color: #000; padding: 16px;">
        <img
            src="{{ asset('item/COACHTECHヘッダーロゴ.png') }}"
            alt="COACHTECH"
            style="height: 32px;"
        >
    </header>

    {{-- メインコンテンツ --}}
    <main style="padding: 40px 0;">
        @yield('content')
    </main>

</body>
</html>
