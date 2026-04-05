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
        Schema::table('blogs', function (Blueprint $table): void {
            $table->foreignId('astrologer_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('review_status')->default('approved')->after('published_at');
            $table->foreignId('reviewed_by')->nullable()->after('review_status')->constrained('users')->nullOnDelete();

            $table->index(['review_status', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropIndex(['review_status', 'is_published']);
            $table->dropColumn('review_status');
            $table->dropConstrainedForeignId('astrologer_id');
        });
    }
};
