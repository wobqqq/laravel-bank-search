<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->autoIncrement();
            $table->string('name')->index();
            $table->string('url');
            $table->boolean('is_active')->default(1)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
