@extends('layouts.app')

@section('title', '店舗・イベントQRコード生成')

@section('content')

    <h2 class="h3 mb-5">店舗・イベント・施設情報</h2>

    <hr>
    <p>
        @if($group->start_at && $group->end_at)
            開催期間:
            {{ \Carbon\Carbon::parse($group->start_at)->format('Y年n月j日 G時i分') }} ～ {{ \Carbon\Carbon::parse($group->end_at)->format('Y年n月j日 G時i分') }}
        @elseif($group->start_at)
            開催期間:
            {{ \Carbon\Carbon::parse($group->start_at)->format('Y年n月j日 G時i分') }} から
        @elseif($group->end_at)
            開催期間:
            {{ \Carbon\Carbon::parse($group->end_at)->format('Y年n月j日 G時i分') }} まで
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
            <td>{{ $group->address }}</td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>{{ $group->telephone }}</td>
        </tr>
        </tbody>
    </table>

@endsection
