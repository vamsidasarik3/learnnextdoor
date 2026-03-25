<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessSettlements extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Settlement';
    protected $name = 'settlement:process';
    protected $description = 'Process automated settlements for providers via Razorpay.';
    protected $usage = 'settlement:process';

    public function run(array $params)
    {
        CLI::write('Starting settlement process...', 'yellow');

        $service = new \App\Services\SettlementService();
        $service->processAutoSettlements();

        CLI::write('Settlement process completed.', 'green');
    }
}
