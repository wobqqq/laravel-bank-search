<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Resource;
use App\Queries\ResourceQuery;
use App\Services\ResourceParsingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class ResourceParser extends Command
{
    private const string STARTING_MESSAGE = 'Start of parsing (%s)';

    private const string ENDING_MESSAGE = 'End of parsing (%s)';

    /** @var string */
    protected $signature = 'app:resource-parser';

    /** @var string */
    protected $description = 'Resource parsing';

    public function handle(ResourceQuery $resourceQuery): void
    {
        $dateAndTime = Carbon::now()->toDateTimeString();

        $this->info(sprintf(self::STARTING_MESSAGE, $dateAndTime));
        Log::info(sprintf(self::STARTING_MESSAGE, $dateAndTime));

        $resources = $resourceQuery->getActiveResources();

        $resources->each(function (Resource $resource) {
            try {
                /** @var ResourceParsingService $ResourceParsingService */
                $ResourceParsingService = app(ResourceParsingService::class, ['resource' => $resource]);
                $ResourceParsingService->serveResource();
            } catch (\Exception|\Throwable $e) {
                $exceptionData = print_r([
                    'title' => sprintf('%s resource error', $resource->name),
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString(),
                ], true);

                $this->error($exceptionData);
                Log::error($exceptionData);
            }
        });

        $this->info(sprintf(self::ENDING_MESSAGE, $dateAndTime));
        Log::info(sprintf(self::ENDING_MESSAGE, $dateAndTime));
    }
}
