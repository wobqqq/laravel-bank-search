<?php

declare(strict_types=1);

use App\Enums\ResourceNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->autoIncrement();
            $table->string('name')->index();
            $table->string('url');
            $table->string('sitemap_url')->nullable();
            $table->boolean('is_active')->default(1)->index();
        });

        $this->seed();
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }

    private function seed(): void
    {
        DB::table('resources')->insert([
            [
                'name' => ResourceNames::INTREB_BANCATRANSILVANIA_RO->value,
                'url' => 'https://intreb.bancatransilvania.ro',
                'is_active' => false,
                'sitemap_url' => 'https://intreb.bancatransilvania.ro/sitemap.xml',
            ],
            [
                'name' => ResourceNames::BLOG_BANCATRANSILVANIA_RO->value,
                'url' => 'https://blog.bancatransilvania.ro',
                'is_active' => false,
                'sitemap_url' => 'https://blog.bancatransilvania.ro/sitemap.xml',
            ],
            [
                'name' => ResourceNames::BANCATRANSILVANIA_RO->value,
                'url' => 'https://www.bancatransilvania.ro',
                'is_active' => false,
                'sitemap_url' => 'https://www.bancatransilvania.ro/sitemap.xml',
            ],
            [
                'name' => ResourceNames::COMUNITATE_BANCATRANSILVANIA_RO->value,
                'url' => 'https://comunitate.bancatransilvania.ro',
                'is_active' => false,
                'sitemap_url' => null,
            ],
            [
                'name' => ResourceNames::BTPENSII_RO->value,
                'url' => 'https://btpensii.ro',
                'is_active' => false,
                'sitemap_url' => null,
            ],
            [
                'name' => ResourceNames::BTMIC_RO->value,
                'url' => 'https://www.btmic.ro',
                'is_active' => false,
                'sitemap_url' => null,
            ],
            [
                'name' => ResourceNames::BTCODECRAFTERS_RO->value,
                'url' => 'https://btcodecrafters.ro',
                'is_active' => false,
                'sitemap_url' => null,
            ],
        ]);
    }
};
