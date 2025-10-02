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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('form_fields')->nullable(); // simpan struktur field sebagai JSON
            $table->boolean('is_public')->default(true);
            $table->string('slug')->unique();
            $table->string('header_image')->nullable();
            $table->string('theme_color')->default('#4F46E5'); // Default: Indigo
            $table->string('background_color')->default('#F3F4F6'); // Default: Gray-100
            $table->string('font_family')->default('Inter');
            $table->text('thank_you_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
