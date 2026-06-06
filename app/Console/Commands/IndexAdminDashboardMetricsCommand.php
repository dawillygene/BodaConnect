<?php

namespace App\Console\Commands;

use App\Services\AdminDashboardMetricsService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:index-admin-dashboard-metrics')]
#[Description('Index admin dashboard metrics into Elasticsearch for Kibana dashboards')]
class IndexAdminDashboardMetricsCommand extends Command
{
    public function __construct(private readonly AdminDashboardMetricsService $adminDashboardMetricsService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $result = $this->adminDashboardMetricsService->indexSnapshot();

        if (! $result['success']) {
            $this->error(sprintf(
                'Unable to index admin dashboard metrics into [%s]. %s',
                $result['index'],
                $result['error'] ?? 'Unknown Elasticsearch error.'
            ));

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Indexed admin dashboard metrics into [%s] at %s.',
            $result['index'],
            $result['timestamp']
        ));

        return self::SUCCESS;
    }
}
