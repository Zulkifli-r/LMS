<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTeachableRenameMaxAttempts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachables', function (Blueprint $table) {
            $table->renameColumn('max_attempts_acount','max_attempts_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachables', function (Blueprint $table) {
            $table->renameColumn('max_attempts_count', 'max_attempts_acount');
        });
    }
}
