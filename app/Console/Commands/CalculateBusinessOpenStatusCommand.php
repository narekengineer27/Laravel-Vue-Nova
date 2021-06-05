<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Services\Api\BusinessService;
use Illuminate\Console\Command;

class CalculateBusinessOpenStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:hours:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates if the business is open at the moment';

    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * Create a new command instance.
     *
     * @param BusinessService $businessService
     */
    public function __construct(BusinessService $businessService)
    {
        parent::__construct();
        $this->businessService = $businessService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $time = microtime(true);
        $businesses = Business::all();
        $this->info('Starting calculation for business open status.');
        foreach ($businesses as $business) {
            $this->businessService->calculateOpenStatus($business);
        }
        $time2 = microtime(true);
        $diff = $time2 - $time;
        $bc = count($businesses);
        $this->info('Calculations completed for '. $bc . ' businesses in '. $diff . ' seconds');
        return;
    }
}
