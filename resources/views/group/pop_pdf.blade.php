<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title></title>
    <style type="text/css">
        @font-face {
            font-family: ipag;
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/ipag.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: ipag;
            font-style: bold;
            font-weight: bold;
            src: url('{{ storage_path('fonts/ipag.ttf') }}') format('truetype');
        }
        body {
            font-family: ipag !important;
        }
    </style>
</head>

<body>

<h1>卓上三角POPのテンプレートファイルです。</h1>
<p>このファイルにシステム固有の卓上三角POPデザインを設定してください。</p>
<p><img src="data:image/png;base64,{{ $src }}" alt=""></p>

</body>
</html>
