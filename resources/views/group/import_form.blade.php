@extends('layouts.app')

@section('title', '店舗・イベント等データ取込み')

@section('content')

    <h2 class="h3 mb-5">店舗・イベント等データ取込み</h2>

    <form method="POST" action="" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="csv" name="csv" accept=".csv">
                            <label class="custom-file-label" for="csv" data-browse="ファイルを選択">CSVファイルを指定してください</label>
                        </div>
                    </div>
                </div>
                @error('csv')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                @if(session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                <p>
                    <a href="/sample/sample_data.xlsx" target="_blank">Excelファイル形式のサンプルをダウンロード</a>
                </p>
                <small class="text-muted">※2MB以内のCSV形式ファイルで指定してください。<br>
                    ※件数によっては処理に時間がかかります。完了するまでお待ちください。<br>
                    ※メールは取込み完了後に順次送信されます。<br>
                    ※大量のメールを送信しますので、お使いの受信環境によっては正常に受信できない場合があります。その場合はシステム管理者にご連絡ください。<br>
                </small>
            </div>
        </div>

        <hr class="mt-5 mb-5">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <button id="submit_button" class="btn btn-primary btn-lg btn-block" type="submit">取込み開始</button>
            </div>
        </div>

    </form>
@endsection


@section('scripts')
    <script type="text/javascript">
        (function () {
            window.addEventListener('load', function () {
                var input = document.getElementById('csv');
                input.addEventListener('change', function (event) {
                    var label = document.querySelector('[for="csv"]');
                    label.textContent = event.target.files[0].name;
                }, false);
            }, false);
        })();
    </script>
@endsection
