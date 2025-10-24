
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
        Schema::create('access_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->foreignId('entry_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exit_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('access_type', ['entry', 'exit']);
            $table->string('access_method', 50); // manual, qr
            $table->string('reference_code', 100)->nullable(); // código QR o código manual
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'date']);
            $table->index('access_type');
            $table->index('access_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_records');
    }
};

