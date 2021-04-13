<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mailAutomater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:automate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AIA automate emails.';

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
        //
    }
}
