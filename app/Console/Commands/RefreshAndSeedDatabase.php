<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshAndSeedDatabase extends Command
{
    protected $signature = 'db:refresh-and-seed';
    protected $description = 'Refresh the database and run all seeders';

    public function handle()
    {
        $this->call('migrate:fresh', ['--seed' => true]);
        $this->info('Database refreshed and seeded successfully.');
    }
}
