<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BasicTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->JSON('quizJSON');
            $table->JSON('dartsJSON');
            $table->bigInteger('subject_id')->unsigned();
            $table->foreign('subject_id')->references('id')->on('subjects');
        });
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->JSON('studentsJSON');
            $table->bigInteger('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
        });
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('players_amount');
            $table->date('date');
            $table->json('QuizStats');
            $table->json('DartsStats');
            $table->bigInteger('quiz_id')->unsigned();
            $table->bigInteger('teacher_id')->unsigned();
            $table->bigInteger('group_id')->unsigned();
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->foreign('quiz_id')->references('id')->on('quizzes');
            $table->foreign('group_id')->references('id')->on('groups');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('school_id')->unsigned()->nullable();
            $table->foreign('school_id')->references('id')->on('schools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('groups');
    }
}
