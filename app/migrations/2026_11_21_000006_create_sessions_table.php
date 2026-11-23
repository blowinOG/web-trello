<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('sessions')) {
            Capsule::schema()->create('sessions', function (Blueprint $table) {
                $table->string('id', 128)->primary();
                $table->text('payload');
                $table->unsignedInteger('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('sessions');
    }
};
