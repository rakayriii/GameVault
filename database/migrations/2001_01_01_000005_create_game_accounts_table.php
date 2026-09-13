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
        Schema::create('game_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('strike_price')->nullable();
            $table->smallInteger('discount_percent')->nullable();
            $table->string('server')->nullable();
            $table->string('region')->nullable();
            $table->string('rank')->nullable();
            $table->string('rank_tier')->nullable();
            $table->smallInteger('level')->nullable();
            $table->smallInteger('heros_count')->nullable();
            $table->smallInteger('skins_count')->nullable();
            $table->decimal('winrate', 5, 2)->nullable();
            $table->string('in_game_balance')->nullable();
            $table->json('rarity_metrics')->nullable();
            $table->json('features')->nullable();
            $table->boolean('instant_delivery')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->text('handover_data')->nullable();
            $table->string('handover_note')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_accounts');
    }
};
