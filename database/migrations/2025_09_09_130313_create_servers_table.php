<?php

use App\Enums\Server\Provider;
use App\Enums\Server\Status;
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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address')->unique();
            $table->enum('provider', Provider::values());
            $table->enum('status', Status::values())->default(Status::ACTIVE->value);
            $table->unsignedTinyInteger('cpu_cores');
            $table->unsignedInteger('ram_mb');
            $table->unsignedInteger('storage_gb');
            $table->timestamps();

            $table->unique(['provider', 'name'], 'provider_name_unique');
            $table->index('provider');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
