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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_category_id')
                ->constrained('service_categories')
                ->restrictOnDelete();
                $table->index('service_category_id');

            $table->string('title', 255);
            $table->string('slug', 255)->unique();

            $table->text('short_description')->nullable();
            $table->text('description');

            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->timestamp('published_at')->nullable()->index();

            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};