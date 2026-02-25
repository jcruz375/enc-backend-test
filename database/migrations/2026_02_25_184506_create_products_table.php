<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('title')->fulltext();
            $table->decimal('price', 8, 2)->index();
            $table->text('description');
            $table->string('category')->index();
            $table->string('image', 2048)->nullable();
            $table->decimal('rating_rate', 3, 1)->nullable()->index();
            $table->unsignedInteger('rating_count')->default(0);
            $table->json('update_log')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
