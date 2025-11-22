<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->text('note')->nullable();
            $table->date('date')->nullable();
            $table->string('receipt_path', 255)->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
