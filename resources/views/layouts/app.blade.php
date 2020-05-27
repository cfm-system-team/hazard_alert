<!doctype html>
<html class="h-100" lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="{{ config('app.name') }}の@yield('title')">
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:title" content="@yield('title')"/>
    <meta property="og:description" content="{{ config('app.name') }}の@yield('title')"/>
    <meta property="og:site_name" content="{{ config('app.name') }}"/>
    <meta property="og:image" content=""/>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style type="text/css">
        footer a {
            color: white !important;
        }
    </style>

    <title>@yield('title') | {{ config('app.name') }}</title>
</head>

<body class="bg-light d-flex flex-column h-100">

<header>
    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <h1 class="h3 text-light p-2">{{ config('app.name') }}</h1>
    </nav>
</header>

<main class="bd-content p-5">
    @yield('content')
</main>

<footer class="footer mt-auto bg-dark p-4 text-light">
    <p>&copy; {{ env('COPYRIGHT') }}</p>
    <p><a href="/terms_user">利用規約（利用者用）</a>&nbsp;|&nbsp;&nbsp;<a href="/terms_organization">利用規約（事業者用）</a>&nbsp;|&nbsp;&nbsp;<a href="{{ env('PRIVACY_POLICY_URL') }}" target="_blank">プライバシーポリシー</a></p>
    <p><small>※「QRコード」は株式会社デンソーウェーブの登録商標です。</small></p>
</footer>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="//code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

@yield('scripts')

</body>
</html>
