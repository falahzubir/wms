<?php

namespace App\Console\Commands;

use App\Http\Controllers\api\ShippingApiController;
use Illuminate\Console\Command;

class RequestTokenDHL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dhl-token:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will request Token DHL.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shipping = new ShippingApiController;
        $shipping->dhl_generate_access_token();
        return Command::SUCCESS;
    }
}
