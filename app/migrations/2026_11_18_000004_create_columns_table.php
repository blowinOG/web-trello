<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('columns')) {
            Capsule::schema()->create('columns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('board_id')->constrained('boards')->cascadeOnDelete();
                $table->string('title', 120);
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('columns');
    }
};
