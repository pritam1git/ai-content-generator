<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // chat, blog, product
            $table->text('prompt');
            $table->longText('response');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_histories');
    }
};
