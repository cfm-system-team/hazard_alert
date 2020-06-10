<?php

namespace Tests\Browser;

use App\Group;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class GroupTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * 事業者登録テスト
     *
     * @return void
     * @throws Throwable
     */
    public function testRegisterTest()
    {
        $this->browse(function (Browser $browser) {
            $faker = Factory::create();
            $name = $faker->name;
            $browser
                ->visit('/group/register')
                ->type('group[name]', $faker->company)
                ->type('group[owner]', $name)
                ->type('group[telephone]', $faker->randomNumber(9, true))
                ->type('group[email]', $faker->email)
                ->type('group[zip_code]', $faker->randomNumber(7, true))
                ->type('group[address]', $faker->address)
                ->radio('group[has_period]', 'true')
                ->type('group[start_at]', now()->format('Y-m-d H:i:00'))
                ->type('group[end_at]', now()->addDays(5)->format('Y-m-d H:i:00'))
                ->check('group[agreed]')
                ->click('#submit_button')
                ->assertSee('登録が完了しました')
                ->assertSee($name);
        });
    }

    /**
     * 事業者照会テスト
     *
     * @return void
     * @throws Throwable
     */
    public function testShowTest()
    {
        $this->browse(function (Browser $browser) {
            $group = factory(Group::class)->create();
            $browser
                ->visit('/group/' . $group->hash)
                ->assertSee($group->name);
        });
    }
}
