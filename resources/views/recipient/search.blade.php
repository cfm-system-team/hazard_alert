<?php
/**
 * @var Collection|Group $groups
 * 店舗・イベント情報
 */

use App\Group;
use phpDocumentor\Reflection\Types\Collection;
?>

@extends('layouts.app')

@section('title', 'メールアドレスのダウンロード')

@section('content')
    <form action="/recipient/search" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="group">店舗・イベント名等 <span class="badge badge-danger">必須</span></label>
                    <select class="form-control select2" name="search[group_id]" class="@error('search.group_id') is-invalid @enderror form-control">
                        <option></option>
                        @foreach($groups as $key => $group)
                            @if($group->id == old('search.group_id'))
                                <option value="{{$group->id}}" data-key="{{$key}}" selected>{{$group->name}}</option>
                            @else
                                <option value="{{$group->id}}" data-key="{{$key}}">{{$group->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('search.group_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div id="group_info" style="display: none">
                    <p id="address"></p>
                    <p id="telephone"></p>
                </div>
            </div>
        </div>

        <hr class="mt-2">

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="start_at">登録日時の範囲（開始）</label>
                    <input id="start_at" name="search[start_at]" type="text" value="{{ old('search.start_at') }}" class="@error('search.start_at') is-invalid @enderror form-control">
                    @error('search.start_at')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="end_at">登録日時の範囲（終了）</label>
                    <input id="end_at" name="search[end_at]" type="text" value="{{ old('search.end_at') }}" class="@error('search.end_at') is-invalid @enderror form-control">
                    @error('search.end_at')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <p>
                        <small class="text-muted">登録日時の範囲を指定しなかった場合、対象のメールアドレスが全件出力されます</small><br>
                        <small class="text-muted">登録日時の範囲を指定する場合、開始・終了のどちらも入力する必要があります</small>
                    </p>
                </div>
            </div>
        </div>

        <hr class="mt-5 mb-5">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <button class="btn btn-primary btn-lg btn-block" type="submit">検索</button>
            </div>
        </div>
    </form>

    @if(isset($recipients_count))
        <hr class="mt-5 mb-5">
        <h3 class="h3 mb-3">検索結果：{{$recipients_count}}件</h3>
        <p>店舗・イベント名等　　：{{$searched['group_name']}}</p>
        <p>登録日時の範囲（開始）：{{$searched['start_at']}}</p>
        <p>登録日時の範囲（終了）：{{$searched['end_at']}}</p>
        @if($recipients_count != 0)
            <form action="/recipient/download" method="post">
                @csrf
                <input type="hidden" name="download[group_id]" value="{{$searched['group_id']}}">
                <input type="hidden" name="download[start_at]" value="{{$searched['start_at']}}">
                <input type="hidden" name="download[end_at]" value="{{$searched['end_at']}}">
                <hr class="mt-5 mb-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <button class="btn btn-primary btn-lg btn-block" type="submit">検索結果をダウンロード</button>
                    </div>
                </div>
                <p style="text-align: center"><small class="text-muted">ダウンロードしたcsvファイルが開けない場合、ファイル名を変更すると開ける場合があります</small></p>
            </form>
        @endif

    @endif
@endsection

@section('scripts')
    <link href="//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css">

    <script type="text/javascript">
        $(function(){
            $('.select2').select2({
                theme: 'bootstrap',
                placeholder: 'リストから選択してください'
            });
        });
    </script>

    <script type="text/javascript">
        $(function()
        {
            jQuery.datetimepicker.setLocale('ja');
            $("[name='search[start_at]']").datetimepicker({
                step:10,
                format:'Y-m-d H:i:00'
            });
        });
    </script>

    <script type="text/javascript">
        $(function()
        {
            jQuery.datetimepicker.setLocale('ja');
            $("[name='search[end_at]']").datetimepicker({
                step:10,
                format:'Y-m-d H:i:00'
            });
        });

        $(function() {
            groups = <?php echo $groups;?>;
            $('select').change(function() {
                let key = $("option:selected", this).data('key');
                $('p#address').text("住所　　：" + groups[key].address);
                $('p#telephone').text("電話番号：" + groups[key].telephone);
                $('#group_info').show();
            });
        });
    </script>
@endsection
