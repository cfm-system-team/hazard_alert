<?php

namespace App\Http\Controllers;

use App\Exceptions\CsvNumberOfColumnsException;
use App\Exceptions\CsvValidationException;
use App\Group;
use App\Jobs\GroupImportMailJob;
use App\Mail\GroupRegistered;
use DB;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;
use Validator;

class GroupController extends Controller
{
    /**
     * グループの情報を表示。
     *
     * @param string $hash グループのハッシュ。
     * @return View
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
     * @return View
     */
    public function create()
    {
        return view('group.create', []);
    }

    /**
     * 新しいグループを登録。
     *
     * @param Request $request
     * @return View
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
     * @param string $hash グループのハッシュ。
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
     * @param string $hash グループのハッシュ。
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
     * @param int $size 画像の一辺の大きさ。
     * @param string $hash グループのハッシュ。
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
     * CSVファイルアップロード画面を表示。
     *
     * @return View
     */
    public function importForm()
    {
        return view('group.import_form');
    }

    /**
     * CSVファイルアップロード登録。
     *
     * @param Request $request
     * @return mixed
     * @throws Throwable
     */
    public function import(Request $request)
    {
        // ファイルバリデーション。
        $request->validate([
            'csv' => 'required|file|max:2048|mimes:csv,txt|mimetypes:text/plain'
        ]);

        $rule = [
            'group.name' => 'required|max:255',
            'group.owner' => 'required|max:255',
            'group.telephone' => 'required|numeric|digits_between:8,11',
            'group.email' => 'required|email|max:255',
            'group.zip_code' => 'required|numeric|digits:7',
            'group.address' => 'required|max:255',
            'group.start_at' => 'nullable|date',
            'group.end_at' => 'nullable|date|after_or_equal:group.start_at',
            'group.type' => 'nullable|max:255'
        ];

        $file = $request->file('csv');

        $config = new LexerConfig();
        $config->setToCharset('UTF-8');
        $config->setFromCharset('SJIS-win');

        $interpreter = new Interpreter();
        // 厳密チェックを外さないと、自前の列数チェックを行う前に例外が発生してしまう。
        $interpreter->unstrict();

        // メール送信用配列。
        // データに1行でも不備がある場合ロールバックするので、
        // すべてのインサート試行を終えた後でメール送信しなければならない。
        $groups = [];

        // ヘッダーの列数。列数チェックに使用する。
        $number_of_header_columns = 0;
        $row_number = 0;
        $interpreter->addObserver(function (array $row) use (&$number_of_header_columns, &$row_number, $rule, &$groups) {

            // 1行目はヘッダーなので、列数を記録してスキップ。
            if ($row_number === 0) {
                $number_of_header_columns = count($row);
                ++$row_number;
                return 0;
            }

            // 列数チェック。
            if (count($row) !== $number_of_header_columns) {
                $message = "{$row_number}行目: 列の数が正しくありません。";
                ++$row_number;
                throw new CsvNumberOfColumnsException($message);
            }

            // バリデーションをかけるためにキーに名前を設定する。
            $key_attached_row = [
                'group' => [
                    'name' => $row[0],
                    'zip_code' => $row[1],
                    'address' => $row[2],
                    'start_at' => $row[3],
                    'end_at' => $row[4],
                    'owner' => $row[5],
                    'telephone' => $row[6],
                    'email' => $row[7]
                ]
            ];

            $validator = Validator::make($key_attached_row, $rule);
            if ($validator->fails()) {
                ++$row_number;
                throw new CsvValidationException("{$row_number}行目: {$validator->errors()->first()}");
            }

            $group_array = $key_attached_row['group'];

            // 規約同意日時を記録。
            $group_array['agreed_at'] = now()->format('Y-m-d H:i:s');

            // hashを生成。重複した場合は生成し直す。
            do {
                $hash = hash('sha256', Str::random(60));
            } while (Group::withTrashed()->where('hash', $hash)->exists());

            $group_array['hash'] = $hash;

            // 空文字のデータがDBのフォーマットチェックにひっかかるのでキーを削除。
            $group_array = array_filter($group_array, function ($value) {
                return $value !== '';
            });

            $group = new Group();
            $group->fill($group_array)->save();
            $group->refresh();

            Log::info(sprintf('グループをインポート登録: ID=%d HASH=%s', $group['id'], $group['hash']));

            $groups[] = $group;

            ++$row_number;
            return 0;
        });

        DB::beginTransaction();
        $lexer = new Lexer($config);
        try {
            $lexer->parse($file, $interpreter);
        } catch (CsvNumberOfColumnsException $exception) {
            DB::rollback();
            return view('group.import_form')->withErrors(['csv' => $exception->getMessage()]);
        } catch (CsvValidationException $exception) {
            DB::rollback();
            return view('group.import_form')->withErrors(['csv' => $exception->getMessage()]);
        } catch (Exception $exception) {
            DB::rollback();
            Log::error('グループCSVインポートエラー: ' . $exception->getMessage());
            return view('group.import_form')->withErrors(['csv' => 'エラーが発生しました。システム管理者にお問い合わせ下さい。']);
        }
        DB::commit();

        GroupImportMailJob::dispatch($groups);
        // ジョブはワーカーを起動しないと実行されないので、コマンドで即実行。
        $cmd = "nohup php " . base_path() . "/artisan queue:work --once > /dev/null &";
        exec($cmd);

        return redirect('group/import')->with('message', sprintf('登録を完了しました（%d件）。', count($groups)));
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
