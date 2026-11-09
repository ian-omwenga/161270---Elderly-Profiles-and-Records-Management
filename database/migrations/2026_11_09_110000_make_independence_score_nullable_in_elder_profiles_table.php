<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elder_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('independence_score')->nullable()->default(70)->change();
        });
    }

    public function down(): void
    {
        Schema::table('elder_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('independence_score')->nullable(false)->default(70)->change();
        });
    }
};
