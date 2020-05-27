@extends('layouts.app')

@section('title', 'メールアドレス登録')

@section('content')
    <p class="mb-3">
        こちらは「{{ config('app.name') }}」来店・来場者様用のご登録ページです。<br>
        万が一、こちらの店舗もしくはイベントにて新型コロナウイルスの感染者が発生した場合、
        濃厚接触者と思われる方へメールでご連絡させていただきます。<br>
        下記入力欄に皆様のメールアドレスのご登録をお願いいたします。
    </p>

    <hr>
    <p>
        @if(!empty($period))
            {{$period}}
        @endif
    </p>
    <h3 class="h4">
        {{ $group->name }}
    </h3>
    <hr class="mb-5">

    <form action="/recipient/register" method="post">
        @csrf

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">メールアドレスを入力してください <span class="badge badge-danger">必須</span></label>
                    <input id="email" name="recipient[email]" type="email" value="{{ old('recipient.email') }}" class="@error('recipient.email') is-invalid @enderror form-control">
                    @error('recipient.email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row" id="confirm">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email_confirmation">確認のためメールアドレスをもう一度入力してください <span class="badge badge-danger">必須</span></label>
                    <input id="email_confirmation" name="recipient[email_confirmation]" type="email" value="{{ old('recipient.email_confirmation') }}" class="@error('recipient.email_confirmation') is-invalid @enderror form-control">
                    @error('recipient.email_confirmation')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="mt-5 mb-5">

        <div class="row justify-content-center">
            <div class="col-md-8" id="submit_area">
                <p style="text-align: center">
                    <label>
                        @if(empty(old('recipient.check_terms')))
                            <input type="checkbox" id="check_terms" name="recipient[check_terms]" onClick="checkTerms(this.checked);"/>
                        @else
                            <input type="checkbox" id="check_terms" name="recipient[check_terms]" onClick="checkTerms(this.checked);" checked/>
                        @endif
                        <a href="/terms_user" target="__blank">利用規約</a>に同意する
                    </label>
                </p>
                    @error('recipient.check_terms')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                <button id="submit" class="btn btn-primary btn-lg btn-block" type="submit">登録</button>
            </div>
        </div>

        <input type="hidden" name="recipient[group_id]" value="{{$group->id}}">
    </form>
@endsection

@section('scripts')
    <script type="text/javascript">
        function checkTerms(is_checked){
            if(is_checked === true){
                document.getElementById("submit").disabled = false;
            } else {
                document.getElementById("submit").disabled = true;
            }
        }
    </script>

    <script type="text/javascript">
        $(function() {
            if ($('#check_terms').prop('checked') === false ){
                $('#submit').prop("disabled", true);
            }
            if ($("#email").val().length === 0) {
                $("#confirm").hide();
            }
            if ($("#email_confirmation").val().length === 0) {
                $("#submit_area").hide();
            }
        });

        $("#email").on("keyup change", function() {
            if ($("#email").val().length === 0) {
                $("#confirm").hide();
                $("#submit_area").hide();
                $("#email_confirmation").val("");
                $("#check_terms").prop("checked", false);
                $('#submit').prop("disabled", true);
            } else {
                $("#confirm").show();
            }
        });

        $("#email_confirmation").on("keyup change", function() {
            if ($("#email_confirmation").val().length === 0) {
                $("#submit_area").hide();
                $("#check_terms").prop("checked", false);
                $('#submit').prop("disabled", true);
            } else {
                $("#submit_area").show();
            }
        });
    </script>
@endsection
