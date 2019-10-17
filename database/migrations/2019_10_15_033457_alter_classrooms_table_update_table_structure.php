<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClassroomsTableUpdateTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('subject_id');
            $table->dropColumn('teaching_period_id');
            $table->dropColumn('code');
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');

            $table->string('name')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('name');

            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('teaching_period_id')->unsigned();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
        });
    }
}
