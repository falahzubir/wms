<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ShippingApiController;
use Illuminate\Console\Command;

class RequestPosMalaysiaToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posmalaysia-token:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will request Bearer Token Pos Malaysia.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shipping = new ShippingApiController;
        $shipping->posmalaysia_generate_access_token();
        return Command::SUCCESS;
    }
}
