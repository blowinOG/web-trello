<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('cards')) {
            Capsule::schema()->create('cards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('column_id')->constrained('columns')->cascadeOnDelete();
                $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->unsignedInteger('position')->default(0);
                $table->timestamp('due_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('cards');
    }
};
