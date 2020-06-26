<?php

namespace App\Http\Controllers;

use App\Group;
use App\Mail\RecipientRegister;
use App\Recipient;
use App\Rules\EmailExists;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class RecipientController extends Controller
{
    /**
     * @var Recipient 受信者
     */
    private $recipient;

    /**
     * メールアドレス登録画面の表示
     *
     * @param string $hash
     * @return Application|Factory|RedirectResponse|Redirector|View
     * @throws \Exception
     */
    public function registerView($hash){
        // ガラケー対応
        if (empty($_SERVER['HTTPS'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
            if(!((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'FOMA') !== false) || (strpos($ua, 'SoftBank') !== false))) {
                $url = 'https://' . $_SERVER['HTTP_HOST'] . '/recipient/register/' . $hash;
                return redirect($url);
            }
        }
        $group = Group::whereHash($hash)
            ->firstOrFail();
        $period = $this->getPeriod($group);
        return view('recipient/register', ['group' => $group, 'period' => $period]);
    }

    /**
     * メールアドレス登録および
     * 登録完了メール送信
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Throwable
     */
    public function register(Request $request){
        // バリデーションチェック
        $request->validate([
            'recipient.email' => 'required|email|confirmed|max:255',
            'recipient.email_confirmation' => 'required|max:255',
            'recipient.group_id' => 'required|exists:App\Group,id',
            'recipient.check_terms' => 'required|regex:/^on/'
        ]);
        $data = $request->get('recipient');
        $data['agreed_at'] = now();
        unset($data['email_confirmation']);
        unset($data['check_terms']);

        // テーブルに登録
        DB::transaction(function () use ($data) {
            $this->recipient = Recipient::create($data);
        });

        // 投稿者へのメール送信
        $group = Group::find($data['group_id']);
        $mail_data = [
            'posted_at' => now()->format('Y-m-d H:i:s'),
            'group' => $group,
            'id' => $this->recipient->id
        ];

        Mail::to($data['email'])->send(new RecipientRegister($mail_data));

        return view('recipient/done', ['method' => '登録']);
    }

    /**
     * メールアドレス登録解除画面の表示
     *
     * @param string $hash
     * @param Request $request
     * @return Application|Factory|RedirectResponse|Redirector|View
     */
    public function unregisterView($hash, Request $request){
        // idがクエリにない場合、その時点で404エラーとする
        if (empty($request->get('id'))) {
            abort('404');
        }
        // バリデーションチェック
        $request->validate([
            'id' => 'required|exists:App\Recipient,id'
        ]);
        // ガラケー対応
        if (empty($_SERVER['HTTPS'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
            if(!((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'FOMA') !== false) || (strpos($ua, 'SoftBank') !== false))) {
                $url = 'https://' . $_SERVER['HTTP_HOST'] . '/recipient/unregister/' . $hash . '?' . http_build_query($request->all());
                return redirect($url);
            }
        }
        $group = Group::whereHash($hash)
            ->firstOrFail();
        $period = $this->getPeriod($group);
        return view('recipient/unregister', ['group' => $group, 'id' => $request->get('id'), 'period' => $period]);
    }

    /**
     * メールアドレス登録解除
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Throwable
     */
    public function unregister(Request $request){
        // バリデーションチェック
        $request->validate([
            'recipient.email' => 'required|email|max:255',
            'recipient.group_id' => 'required|exists:App\Group,id'
        ]);
        $data = $request->get('recipient');
        // emailとgroup_idのバリデーションチェックを済ませてから、登録idの照合を行う
        $request->validate([
            'recipient.id' => [
                'required',
                new EmailExists($data['email'], $data['group_id'])
            ]
        ]);

        // テーブルから削除
        DB::transaction(function () use ($data) {
            Recipient::whereEmail($data['email'])
                ->whereGroupId($data['group_id'])
                ->delete();
        });

        return view('recipient/done', ['method' => '解除']);
    }

    /**
     * メールアドレスリスト検索画面表示
     *
     * @return Application|Factory|View
     */
    public function searchView(){
        return view('recipient/search');
    }

    /**
     * メールアドレスリスト検索
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function search(Request $request){
        // バリデーションチェック
        $request->validate([
            'search.group_id' => 'required|exists:App\Group,id',
            'search.start_at' => 'nullable|date|required_with:search.end_at',
            'search.end_at' => 'nullable|date|after:search.start_at|required_with:search.start_at'
        ]);
        $data = $request->get('search');

        // メールアドレスリストの件数取得
        $group = Group::withCount(['recipients' => function ($query) use ($data){
            if (!empty($data['start_at'])){
                $query->where('created_at', '>=', $data['start_at']);
                $query->where('created_at', '<=', $data['end_at']);
            }
        }])->findOrFail($data['group_id']);

        $groups = Group::all();
        $data['group_name'] = $group->name;
        return view('recipient/search', [
            'groups' => $groups,
            'searched' => $data,
            'recipients_count' => $group->recipients_count
        ]);
    }

    /**
     * メールアドレスリストダウンロード
     *
     * @param Request $request
     */
    public function download(Request $request){
        // バリデーションチェック
        $request->validate([
            'download.group_id' => 'required|exists:App\Group,id',
            'download.start_at' => 'nullable|date|required_with:download.end_at',
            'download.end_at' => 'nullable|date|after:download.start_at|required_with:download.start_at'
        ]);
        $data = $request->get('download');

        // メールアドレスリストの取得
        $group = Group::with(['recipients' => function ($query) use ($data){
            if (!empty($data['start_at'])){
                $query->where('created_at', '>=', $data['start_at']);
                $query->where('created_at', '<=', $data['end_at']);
            }
        }])->findOrFail($data['group_id']);

        // csvファイルとして出力
        $text = '"店舗・イベント名等","期間","住所","登録日時","メールアドレス"' . PHP_EOL;
        foreach ($group->recipients as $recipient) {
            $text .= '"' . str_replace('"', '""',$group->name) . '","';
            if (!empty($data['start_at'])){
                $text .= $data['start_at'] . '～' . $data['end_at'] . '","';
            } else {
                $text .= '","';
            }
            $text .= str_replace('"', '""', $group->address) . '","' . $recipient->created_at . '","' . $recipient->email . '"' . PHP_EOL;
        }
        $file_name = str_replace('"', '',$group->name) . '_' . now()->format('YmdHis') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $file_name .'"');
        echo mb_convert_encoding($text, 'SJIS', 'UTF-8');
        exit;
    }


    /**
     * 開催期間を文字列として取得
     *
     * @param Group $group
     * @return string
     */
    private function getPeriod(Group $group){
        $period = '';
        if (!empty($group->start_at) && !empty($group->end_at)) {
            $period = '開催期間：' . $group->start_at->format('Y年n月j日 G時i分') . ' ～ ' . $group->end_at->format('Y年n月j日 G時i分');
        } elseif (!empty($group->start_at)){
            $period = '開催期間：' . $group->start_at->format('Y年n月j日 G時i分') . ' から';
        } elseif (!empty($group->end_at)) {
            $period = '開催期間：' . $group->end_at->format('Y年n月j日 G時i分') . ' まで';
        }
        return $period;
    }
}
