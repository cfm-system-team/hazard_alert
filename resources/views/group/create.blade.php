@extends('layouts.app')

@section('title', '店舗・イベントQRコード生成')

@section('content')

    <h2 class="h3 mb-4">店舗・イベント・施設QRコード生成</h2>

    <p class="mb-5">
        こちらは「{{ config('app.name') }}」店舗関係者・イベント主催者様用のご登録ページです。<br>
        下記入力欄に皆様の店舗・イベント・集客施設の情報のご登録をお願いします。<br>
        「登録してQRコードを生成」のボタンを押すと、掲示用のQRコードをダウンロードすることができます。<br>
        また、登録いただいた情報はメール受信を希望するユーザーに公開されます。
    </p>

    <form method="POST" action="" class="h-adr">
        @csrf
        <span class="p-country-name" style="display:none;">Japan</span>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="group[name]">店舗（支店）・イベント・施設名 <span class="badge badge-danger">必須</span></label>
                    <input id="group[name]" name="group[name]" type="text" value="{{ old('group.name') }}" class="@error('group.name') is-invalid @enderror form-control">
                    @error('group.name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="group[owner]">担当者氏名 <span class="badge badge-danger">必須</span></label>
                    <input id="group[owner]" name="group[owner]" type="text" value="{{ old('group.owner') }}" class="@error('group.owner') is-invalid @enderror form-control">
                    @error('group.owner')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="group[telephone]">連絡先電話番号 <span class="badge badge-danger">必須</span></label>
                    <input id="group[telephone]" name="group[telephone]" type="text" value="{{ old('group.telephone') }}" class="@error('group.telephone') is-invalid @enderror form-control">
                    @error('group.telephone')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">記号無し、数字のみで入力してください。</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="group[email]">担当者メールアドレス <span class="badge badge-danger">必須</span></label>
                    <input id="group[email]" name="group[email]" type="text" value="{{ old('group.email') }}" class="@error('group.email') is-invalid @enderror form-control">
                    @error('group.email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="group[zip_code]">郵便番号 <span class="badge badge-danger">必須</span></label>
                    <input id="group[zip_code]" name="group[zip_code]" type="text" value="{{ old('group.zip_code') }}" class="p-postal-code @error('group.zip_code') is-invalid @enderror form-control">
                    @error('group.zip_code')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">記号無し、数字のみで入力してください。</small>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="group[address]">住所 <span class="badge badge-danger">必須</span></label>
                    <input id="group[address]" name="group[address]" type="text" value="{{ old('group.address') }}" class="p-region p-locality p-street-address p-extended-address @error('group.address') is-invalid @enderror form-control">
                    @error('group.address')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-8 radio-inline">
                <label class="mr-4">
                    <input type="radio" name="group[has_period]" value="false" onClick="changePeriodRowVisibility();" @if(old('group.has_period')==='false' || empty(old('group.has_period'))) checked @endif/>
                    開催期間無し
                </label>
                <label>
                    <input type="radio" name="group[has_period]" value="true" onClick="changePeriodRowVisibility();" @if(old('group.has_period')==='true') checked @endif/>
                    開催期間有り
                </label>
            </div>
        </div>

        <div id="period_row" class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="group[start_at]">開始日時</label>
                    <input id="group[start_at]" name="group[start_at]" type="text" value="{{ old('group.start_at') }}" class="@error('group.start_at') is-invalid @enderror form-control">
                    @error('group.start_at')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">イベントなどの開始日時が決まっている場合はご記入ください。</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="group[end_at]">終了日時</label>
                    <input id="group[end_at]" name="group[end_at]" type="text" value="{{ old('group.end_at') }}" class="@error('group.end_at') is-invalid @enderror form-control">
                    @error('group.end_at')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">イベントなどの終了日時が決まっている場合はご記入ください。</small>
                </div>
            </div>
        </div>

        <hr class="mt-5 mb-5">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <p style="text-align: center">
                    <label>
                        <input type="checkbox" id="group[agreed]" name="group[agreed]" value="true" onClick="syncSubmitButtonEnabled();" @if(old('group.agreed')) checked @endif/>
                        <a href="/terms_organization" target="__blank">利用規約</a>に同意する
                    </label>
                </p>
                @error('group.agreed')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <button id="submit_button" class="btn btn-primary btn-lg btn-block" type="submit">登録してQRコードを生成</button>
            </div>
        </div>

    </form>
@endsection


@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css">
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>

    <script type="text/javascript">
        // 送信ボタン制御。
        function syncSubmitButtonEnabled() {
            document.getElementById('submit_button').disabled = !document.getElementById('group[agreed]').checked;
        }

        function changePeriodRowVisibility() {
            has_period = $("input[name='group[has_period]']:checked").val();
            if (has_period === 'false') {
                $("#period_row").hide();
            }
            if (has_period === 'true') {
                $("#period_row").show();
            }
        }

        $(function () {
            jQuery.datetimepicker.setLocale('ja');
            $("[name='group[start_at]']").datetimepicker({
                step: 10,
                format: 'Y-m-d H:i:00'
            });

            jQuery.datetimepicker.setLocale('ja');
            $("[name='group[end_at]']").datetimepicker({
                step: 10,
                format: 'Y-m-d H:i:00'
            });

            syncSubmitButtonEnabled();
            changePeriodRowVisibility();
        });
    </script>

@endsection
