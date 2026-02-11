<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>

    {{-- ヘッダー（ログイン前） --}}
    <header class="header">
        <div class="header-inner">
            <img src="{{ asset('item/COACHTECHヘッダーロゴ.png') }}"
                alt="COACHTECH"
                class="header-logo">
        </div>
    </header>

    <main>
            @yield('content')
        </main>

</body>
</html>