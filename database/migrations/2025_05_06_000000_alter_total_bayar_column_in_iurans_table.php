<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iurans', function (Blueprint $table) {
            $table->unsignedBigInteger('total_bayar')->change();
        });
    }

    public function down(): void
    {
        Schema::table('iurans', function (Blueprint $table) {
            $table->unsignedInteger('total_bayar')->change();
        });
    }
};
