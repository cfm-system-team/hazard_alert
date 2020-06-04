@extends('layouts.mail')

@section('content')
{{ config('app.name') }}のご利用ありがとうございます。
QRコードの生成が完了しました。

---------------------------------------
@if($group->start_at && $group->end_at)
■開催期間
{{ $group->start_at->format('Y年n月j日 G時i分') }} ～ {{ $group->end_at->format('Y年n月j日 G時i分') }}
@elseif($group->start_at)
■開催期間
{{ $group->start_at->format('Y年n月j日 G時i分') }} から
@elseif($group->end_at)
■開催期間
{{ $group->end_at->format('Y年n月j日 G時i分') }} まで
@endif

■店舗名・イベント名等
{{ $group->name }}
{{ $group->address }}
TEL: {{ $group->telephone }}

---------------------------------------

■ポスター用PDFファイルのダウンロード
{{ url("group/poster_pdf/{$group->hash}") }}

■卓上三角POP用PDFファイルのダウンロード
{{ url("group/pop_pdf/{$group->hash}") }}
※三つ折りにしてテーブルの上の三角POPとしてご使用いただけます。

■PNG画像（120x120 px）のダウンロード
{{ url("group/png/120/{$group->hash}") }}

■PNG画像（240x240 px）のダウンロード
{{ url("group/png/240/{$group->hash}") }}

■PNG画像（480x480 px）のダウンロード
{{ url("group/png/480/{$group->hash}") }}

---------------------------------------
@endsection
