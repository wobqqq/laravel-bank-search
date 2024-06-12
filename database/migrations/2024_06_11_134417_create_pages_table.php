<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->autoIncrement();
            $table->string('url', 2000);
            $table->boolean('is_active')->default(1)->index();
            $table->smallInteger('resource_id')->unsigned()->index();
            $table->string('title', 500);
            $table->text('content');
            $table->timestamps();

            $table->foreign('resource_id')
                ->references('id')
                ->on('resources');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
