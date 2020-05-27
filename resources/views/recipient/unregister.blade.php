@extends('layouts.app')

@section('title', 'メールアドレス登録解除')

@section('content')
    <p class="mb-3">
        こちらは「{{ config('app.name') }}」にご登録されたメールアドレスを解除するページです。<br>
        登録を解除されますと、こちらの店舗・イベント等にて新型コロナウイルスの感染者が発生した場合、注意喚起のメールを受信できなくなります。
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

    <form action="/recipient/unregister" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">メールアドレス <span class="badge badge-danger">必須</span></label>
                    <input id="email" name="recipient[email]" type="email" value="{{ old('recipient.email') }}" class="@error('recipient.email') is-invalid @enderror form-control">
                    @error('recipient.email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @error('recipient.id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="mt-5 mb-5">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <button class="btn btn-primary btn-lg btn-block" type="submit">解除</button>
            </div>
        </div>

        <input type="hidden" name="recipient[group_id]" value="{{$group->id}}">
        <input type="hidden" name="recipient[id]" value="{{$id}}">
    </form>
@endsection
