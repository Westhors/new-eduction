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
        Schema::table('teachers', function (Blueprint $table) {

            // drop foreign key first
            if (Schema::hasColumn('teachers', 'stage_id')) {
                $table->dropForeign(['stage_id']); // drop FK
                $table->dropColumn('stage_id');    // then drop column
            }

            if (Schema::hasColumn('teachers', 'subject_id')) {
                $table->dropForeign(['subject_id']); // drop FK
                $table->dropColumn('subject_id');    // then drop column
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('stage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
