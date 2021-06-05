<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetUpElasticIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:setup-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-stop shop to set up ElasticSearch indexes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $toIndex = [
            'App\Elastic\Configurators\Business',
            'App\Elastic\Configurators\BusinessAttribute',
            'App\Elastic\Configurators\BusinessReview',
            'App\Elastic\Configurators\BusinessPost',
            'App\Elastic\Configurators\Category'
        ];

        foreach ($toIndex as $index) {
            Artisan::call('elastic:create-index', ['index-configurator' => $index]);
        }
    }
}
