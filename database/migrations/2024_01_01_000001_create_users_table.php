<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        Capsule::schema()->create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('email', 255);
            $table->string('password_hash', 255);
            $table->string('name', 255);
            $table->timestamp('created_datetime');
            $table->timestamp('updated_datetime')->nullable();
        });
        Capsule::statement('CREATE UNIQUE INDEX idx_email ON users (email)');
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('users');
    }
};