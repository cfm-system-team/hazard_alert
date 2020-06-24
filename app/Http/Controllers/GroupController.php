<?php

namespace App\Http\Controllers;

use App\Group;
use App\Mail\GroupRegistered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GroupController extends Controller
{
    /**
     * グループの情報を表示。
     *
     * @param $hash
     * グループのハッシュ。
     * @return Response
     */
    public function show($hash)
    {
        $group = Group::where('hash', $hash)->firstOrFail();

        $data = compact('group');
        return view('group.show', $data);
    }

    /**
     * 新しいグループの登録フォームを表示。
     *
     * @return Response
     */
    public function create()
    {
        return view('group.create', []);
    }

    /**
     * 新しいグループを登録。
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rule = [
            'group.name' => 'required|max:255',
            'group.owner' => 'required|max:255',
            'group.telephone' => 'required|numeric|digits_between:8,11',
            'group.email' => 'required|email|max:255',
            'group.zip_code' => 'required|numeric|digits:7',
            'group.address' => 'required|max:255',
            'group.agreed' => 'required',
        ];

        if ($request->input('group.has_period') === 'true') {
            $rule['group.start_at'] = 'nullable|required_without:group.end_at|date';
            $rule['group.end_at'] = 'nullable|required_without:group.start_at|date|after_or_equal:group.start_at';
        }

        $request->validate($rule);

        $request_group = $request->input('group');

        // 開催期間無しに設定されていたら開催期間を削除。
        if ($request->input('group.has_period') === 'false') {
            unset($request_group['start_at']);
            unset($request_group['end_at']);
        }

        // 開催期間有無を削除。
        unset($request_group['has_period']);

        // 規約同意日時を記録。
        $request_group['agreed_at'] = now();
        unset($request_group['agreed']);

        // hashを生成。重複した場合は生成し直す。
        do {
            $hash = hash('sha256', Str::random(60));
        } while (Group::withTrashed()->where('hash', $hash)->exists());

        $request_group['hash'] = $hash;

        $group = new Group();
        $group->fill($request_group)->save();
        $group->refresh();

        // メール送信。
        Mail::to($group['email'])->send(new GroupRegistered(['group' => $group]));

        $data = compact('group');
        return view('group.store', $data);
    }

    /**
     * ポスター用のPDFを表示。
     *
     * @param $hash
     * グループのハッシュ。
     * @return mixed
     */
    public function poster_pdf($hash)
    {
        $group = Group::where('hash', $hash)->firstOrFail();

        $url = str_replace('https://', 'http://', url("recipient/register/{$hash}"));
        $src = base64_encode(QrCode::format('png')
            ->size(240)
            ->margin(8)
            ->generate($url));

        $data = compact('group', 'src');

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4');
        $pdf->loadView('group.poster_pdf', $data);
        return $pdf->stream();
    }

    /**
     * POP用のPDFを表示。
     *
     * @param $hash
     * グループのハッシュ。
     * @return mixed
     */
    public function pop_pdf($hash)
    {
        $group = Group::where('hash', $hash)->firstOrFail();

        $url = str_replace('https://', 'http://', url("recipient/register/{$hash}"));
        $src = base64_encode(QrCode::format('png')
            ->size(240)
            ->margin(6)
            ->generate($url));

        $data = compact('group', 'src');

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('group.pop_pdf', $data);
        return $pdf->stream();
    }

    /**
     * PNG画像を表示。
     *
     * @param $size
     * 画像の一辺の大きさ。
     * @param $hash
     * グループのハッシュ。
     * @return mixed
     */
    public function png($size, $hash)
    {
        // 負荷対策のため、生成サイズは1200x1200まで。
        if (1200 < $size) {
            abort(404);
        }

        // 存在チェック。
        if (!Group::where('hash', $hash)->firstOrFail()->exists()) {
            abort(404);
        }

        $url = str_replace('https://', 'http://', url("recipient/register/{$hash}"));
        $base64 = base64_encode(QrCode::format('png')
            ->size($size)
            ->margin(0)
            ->generate($url));

        return response(base64_decode($base64))->header('Content-type', 'image/png');
    }

    /**
     * Select2用に施設を検索する。
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function select2Search(Request $request){
        $rule = [
            'q' => 'max:255',
            'group_id' => 'exists:App\Group,id'
        ];
        $request->validate($rule);
        if (!empty($request->input('group_id'))) {
            $groups = Group::whereId($request->input('group_id'))->get();
        } elseif (!empty($request->input('q'))) {
            $groups = Group::where('name', 'LIKE', '%' . $request->input('q') . '%')->get();
        } else {
            $groups = Group::all();
        }
        return response()->json(['groups' => $groups]);
    }
}
