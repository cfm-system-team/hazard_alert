<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('zip_code')->after('name');
            $table->string('address')->after('zip_code');
            $table->datetime('start_at')->after('address')->nullable();
            $table->datetime('end_at')->after('start_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('zip_code');
            $table->dropColumn('address');
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }
}
