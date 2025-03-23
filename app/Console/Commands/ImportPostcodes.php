<?php

namespace App\Console\Commands;

use App\Helpers\PostcodeLocationLoaders\PostcodeLocationLoader;
use App\Models\PostcodeLocation;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class ImportPostcodes extends Command
{
    private Builder $builder;

    public function __construct(
        private readonly PostcodeLocationLoader $postcodeLocationLoader,
        PostcodeLocation $postcodeLocation
    ) {
        parent::__construct();

        $this->builder = $postcodeLocation->newQuery();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postcodes:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import postcodes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;

        $this->builder->truncate();

        while ($rows = $this->postcodeLocationLoader->read(1000)) {
            $this->builder->insert($rows);
            $count += count($rows);

            if ($count % 10000 === 0) {
                $this->info("Processing, imported $count rows");
            }
        }

        $this->info("Completed processing, imported $count rows");
    }
}
