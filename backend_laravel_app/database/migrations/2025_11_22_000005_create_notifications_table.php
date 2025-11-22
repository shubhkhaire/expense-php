<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->tinyInteger('read_flag')->default(0);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
