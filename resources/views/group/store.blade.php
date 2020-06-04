@extends('layouts.app')

@section('title', '店舗・イベントQRコード生成')

@section('content')

    <h2 class="mb-5">登録が完了しました</h2>

    <p class="mb-5">掲出用ポスター、および卓上三角POP用ファイルのデータ、およびQRコードをダウンロードできるリンクは、ご登録のメールアドレス宛に送らせていただいた自動メール内に記載がございますので、ご確認ください。</p>

    <hr>
    <p>
        @if($group->start_at && $group->end_at)
            開催期間:
            {{ $group->start_at->format('Y年n月j日 G時i分') }} ～ {{ $group->end_at->format('Y年n月j日 G時i分') }}
        @elseif($group->start_at)
            開催期間:
            {{ $group->start_at->format('Y年n月j日 G時i分') }} から
        @elseif($group->end_at)
            開催期間:
            {{ $group->end_at->format('Y年n月j日 G時i分') }} まで
        @endif
    </p>
    <h3 class="h4">
        {{ $group->name }}
    </h3>
    <hr class="mb-5">

    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>住所</th>
            <td>
                〒{{ substr($group->zip_code ,0,3) . "-" . substr($group->zip_code ,3) }}<br>
                {{ $group->address }}
            </td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>{{ $group->telephone }}</td>
        </tr>
        <tr>
            <th>担当者氏名</th>
            <td>{{ $group->owner }}</td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>{{ $group->email }}</td>
        </tr>
        </tbody>
    </table>

@endsection
