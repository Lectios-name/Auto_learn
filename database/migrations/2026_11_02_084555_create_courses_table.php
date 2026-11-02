<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('access_type')->default('paid');   // free | paid | subscription
            $table->decimal('price', 10, 2)->nullable();
            $table->string('level')->default('beginner');     // beginner | intermediate | advanced
            $table->string('format')->default('mixed');      // video | text | practice | mixed
            $table->string('status')->default('draft');       // draft | on_moderation | published | rejected
            $table->decimal('rating_avg', 3, 2)->unsigned()->default(0);   // рейтинг 0.00–9.99
            $table->unsignedInteger('rating_count')->default(0);           // эту можно оставить как есть
            $table->timestamps();

            $table->index(['status', 'access_type', 'level']); // для фильтров каталога
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
