@extends('layouts.app')

@section('title', "メールアドレス{$method}")

@section('content')
    <p>
        メールアドレスの{{$method}}が完了しました。
        @if($method == '登録')
            <br>
            自動返信メールが届かない場合は、迷惑メールフォルダをご確認のうえ、入力したメールアドレスが間違っていないかご確認ください。
            <br>
            また、ドメイン指定による受信設定をされている場合、ドメイン「{{ env('APP_DOMAIN') }}」を受信できる設定になっているかご確認ください。
        @endif
    </p>
@endsection
