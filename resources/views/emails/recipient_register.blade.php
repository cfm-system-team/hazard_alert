<?php
/**
 * メールアドレス登録完了メール。
 *
 * @var string $posted_at
 * 投稿日時。
 * @var Collection|Group $group
 * 店舗・イベント情報
 * @var int $id
 * 登録id
 */

use App\Group;
use phpDocumentor\Reflection\Types\Collection;
?>
@extends('layouts.mail')

@section('content')
{{ config('app.name') }}へのご利用者様のメールアドレス登録が完了いたしました。

---------------------------------------
■登録日時
<?php echo $posted_at, PHP_EOL; ?>

■ご利用店舗名・イベント名等
<?php echo $group->name, PHP_EOL; ?>
<?php echo $group->address, PHP_EOL; ?>
TEL:<?php echo $group->telephone, PHP_EOL; ?>
<?php
    echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . "/group/{$group->hash}", PHP_EOL;
?>
---------------------------------------

この登録を解除する場合は、下記のリンクから解除してください。
<?php
    echo (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . "/recipient/unregister/{$group->hash}?id={$id}", PHP_EOL;
?>
@endsection
