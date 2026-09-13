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
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('membership_tier', 20)->default('silver');
            $table->boolean('is_official_verified')->default(false);
            $table->unsignedBigInteger('total_sales')->default(0);
            $table->decimal('rating_cache', 3, 2)->default(0);
            $table->integer('response_time_minutes')->default(2);
            $table->text('announcement')->nullable();
            $table->timestamps();
        });

        Schema::create('seller_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['seller_id', 'follower_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_followers');
        Schema::dropIfExists('seller_profiles');
    }
};
