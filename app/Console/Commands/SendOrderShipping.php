<?php

namespace App\Console\Commands;

use App\Http\Controllers\api\ShippingApiController;
use App\Services\MessageService;
use Illuminate\Console\Command;

class SendOrderShipping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-shipping:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send shipping to BOS.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $qiscusService = new MessageService();
        $shipping = new ShippingApiController($qiscusService);
        $shipping->send_shipping_info();
        return Command::SUCCESS;
    }
}
