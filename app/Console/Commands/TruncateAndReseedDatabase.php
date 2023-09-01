<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TruncateAndReseedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:truncate-and-reseed-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Truncate the database tables
        Artisan::call('migrate:refresh', ['--force' => true]);

        // Reseed the database
        Artisan::call('db:seed', ['--force' => true]);
    }
}
