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
            $table->string('external_id')->index();
            $table->string('content_id')->unique();
            $table->string('meta_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('title', 500)->nullable();
            $table->text('content');
            $table->string('synonyms', 1000)->nullable();
            $table->dateTime('parsed_at');
            $table->dateTime('changed_at');
            $table->timestamps();

            $table->foreign('resource_id')
                ->references('id')
                ->on('resources');

            $table->fullText(['title', 'content', 'synonyms', 'meta_title', 'meta_description'], 'search_index');
            $table->fullText('title');
            $table->fullText('content');
            $table->fullText('synonyms');
            $table->fullText('meta_title');
            $table->fullText('meta_description');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
