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
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropUnique('certificates_certificate_number_unique');
            $table->unique(['course_id', 'certificate_number'], 'certificates_course_id_certificate_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropUnique('certificates_course_id_certificate_number_unique');
            $table->unique('certificate_number', 'certificates_certificate_number_unique');
        });
    }
};
