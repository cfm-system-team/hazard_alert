@extends('layouts.app')

@section('title', "利用規約（利用者）")

@section('content')
    <h2 class="h2 mb-5">利用規約（利用者）</h2>

    <p>QRコードを読み取ってメールアドレスを登録する利用者用の利用規約を定義してください</p>

    <hr class="mt-5 mb-5">

    <div>
        <div>
            <h3>第1条 ○○○</h3>
            説明文
        </div>

        <hr class="mt-5 mb-5">

        <div>
            <h3>第2条 ○○〇</h3>
            説明文
            <ol>
                <li>リスト1</li>
                <li>リスト2</li>
                <li>リスト3</li>
            </ol>
        </div>

        <hr class="mt-5 mb-5">

        <div>
            <h3>第3条 ○○○</h3>
            <ol>
                <li>
                    リスト1
                    <ol>
                        <li>リスト1</li>
                        <li>リスト2</li>
                        <li>リスト3</li>
                    </ol>
                </li>
                <li>
                    リスト2
                </li>
            </ol>
        </div>

        <hr class="mt-5 mb-5">

        <p>以上</p>
        <p>○○○〇年○〇月○〇日　制定</p>
    </div>
@endsection
