<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSocialProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_social_profile', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('nickname')->nullable();
            $table->text('about_me')->nullable();
            $table->text('skills')->nullable();
            $table->text('portfolio')->nullable();
            $table->text('group')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_social_profile');
    }
}
