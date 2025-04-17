<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ServeFileManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serve:file-manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run file manager';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('serve --port=8080');
    }
}
