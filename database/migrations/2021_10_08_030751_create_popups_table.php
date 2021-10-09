<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('link');
            $table->integer('duration')->default(5000)->comment('in milliseconds');
            $table->boolean('is_repetitive')->default(false);
            $table->integer('repeat_period')->default(12)->nullable()->comment('in minutes');
            $table->json('dimentions')->nullable();
            $table->json('styles')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('popups');
    }
}
