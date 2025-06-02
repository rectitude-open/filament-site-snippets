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
        Schema::create('site_snippets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->default('');
            $table->text('content')->nullable();
            $table->string('type')->default('text')->comment('text, html, image');
            $table->string('description')->nullable();

            $table->index('key', 'idx_site_snippets_key');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_snippets');
    }
};
