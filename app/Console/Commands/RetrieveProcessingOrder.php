<?php

namespace App\Console\Commands;

use App\Jobs\SendProcessingOrder;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RetrieveProcessingOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:retrieve-processing-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves processing orders from BOS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $response = Http::get('http://localhost/bos/get_sales');

        $response = Http::get('https://bosemzi.com/get_sales');

        if ($response->successful()) {
            $sales_id = $response->json();
            // Ensure $sales_id is an array
            if (!is_array($sales_id)) {
                $sales_id = (array) $sales_id; // Convert to array if not an array
            }
            foreach ($sales_id as $salesId) {
                if (!Order::where('sales_id', $salesId)->exists()) {
                    SendProcessingOrder::dispatch($salesId);
                }
            }
        }
    }

}