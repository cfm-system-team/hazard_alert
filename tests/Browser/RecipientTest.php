<?php

namespace Tests\Browser;

use App\Group;
use App\Recipient;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class RecipientTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * 施設利用者登録テスト
     *
     * @return void
     * @throws Throwable
     */
    public function testRegisterTest()
    {
        $this->browse(function (Browser $browser) {
            $group = factory(Group::class)->create();
            $faker = Factory::create();
            $email = $faker->email;
            $browser
                ->visit('/recipient/register/' . $group->hash)
                ->type('recipient[email]', $email)
                ->type('recipient[email_confirmation]', $email)
                ->check('recipient[check_terms]')
                ->click('#submit')
                ->assertSee('メールアドレスの登録が完了しました。')
                ->assertSee('自動返信メール');
        });
    }

    /**
     * 施設利用者登録解除テスト
     *
     * @return void
     * @throws Throwable
     */
    public function testUnregisterTest()
    {
        $this->browse(function (Browser $browser) {
            $group = factory(Group::class)->create();
            $recipient = factory(Recipient::class)->create(['group_id' => $group->id]);
            $browser
                ->visit('/recipient/unregister/' . $group->hash . '?id=' . $recipient->id)
                ->type('recipient[email]', $recipient->email)
                ->click('.btn')
                ->assertSee('メールアドレスの解除が完了しました。');
        });
    }

    /**
     * 施設利用者検索テスト
     *
     * @return void
     * @throws Throwable
     */
    public function testSearchTest()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $group = factory(Group::class)->create();
            $recipient = factory(Recipient::class)->create(['group_id' => $group->id]);
            $browser
                ->loginAs($user)
                ->visit('/recipient/search')
                ->select('search[group_id]', $group->id)
                ->type('search[start_at]', now()->subHour())
                ->type('search[end_at]', now()->addHour())
                ->click('.btn')
                ->assertSee('検索結果をダウンロード')
                ->assertSee($group->name);
        });
    }
}
