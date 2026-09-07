<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable()->after('id');
            $table->string('nama')->nullable()->after('kategori');
            $table->enum('metode', ['Tunai', 'Transfer'])->nullable()->after('tanggal');

            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
        });

        Schema::create('transaction_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->string('file_path');
            $table->string('original_name')->nullable();

            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_files');

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn(['student_id', 'nama', 'metode']);
        });
    }
};