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
        Schema::table('todos', function (Blueprint $table) {
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium')->after('title');
            $table->date('due_date')->nullable()->after('priority');
            $table->string('category')->nullable()->after('due_date');
            $table->text('description')->nullable()->after('category');
            $table->integer('order')->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn(['priority', 'due_date', 'category', 'description', 'order']);
        });
    }
};
