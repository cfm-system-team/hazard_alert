<?php

namespace App\Console\Commands;

use App\Recipient;
use DB;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Validator;

class DeleteEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete_email {days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '該当するメールアドレスをrecipientsテーブルから論理削除する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('メールアドレス削除処理開始');
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->error('validation error!');
            foreach ($e->validator->getMessageBag()->all() as $error) {
                $this->error($error);
            }
            return config('command.exit_code.ERROR');
        }
        $days = $this->argument("days");
        // 現在日時から指定した日数分を差し引いた日時を取得
        $delete_date = now()->subDays($days);
        $recipients = Recipient::where('created_at', '<=', $delete_date)->get();
        $this->info('削除対象：' . $delete_date->format('Y-m-d H:i:s') . ' 以前のデータ');
        $this->info('削除対象件数：' . count($recipients));
        foreach ($recipients as $recipient) {
            // テーブル更新
            DB::transaction(function () use ($recipient) {
                Recipient::whereId($recipient->id)->delete();
            });
        }
        $this->info('メールアドレス削除処理終了');
        return config('command.exit_code.SUCCESS');
    }

    /**
     * Validation
     *
     * @throws ValidationException
     */
    private function validate()
    {
        Validator::validate(
            array_filter($this->arguments()),
            [
                'day' => 'integer|max:100'
            ],
            [
                'day.integer' => '日数は整数を入力してください',
                'day.max' => '日数は100日以内で入力してください'
            ]
        );
    }
}
