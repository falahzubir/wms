<?php
namespace App\Console\Commands;

use App\Jobs\SendProcessingOrder;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $companies = Company::all();
        $time = now()->subMinutes(5)->format('Y-m-d H:i:s');
        $time_limit = now()->subDay()->startOfDay()->format('Y-m-d H:i:s');

        foreach ($companies as $company) {

            if($company->url == null || $company->url == '') {
                continue;
            }

            $response = Http::get($company->url . '/wms/get_sales');

            if ($response->successful()) {
                $sales_ids = $response->json();

                $existing_orders = Order::whereIn('sales_id', $sales_ids)
                                        ->where('company_id', $company->id)
                                        ->pluck('sales_id')
                                        ->toArray();

                $order_diff = array_diff($sales_ids, $existing_orders);

                // Dispatch jobs for the new sales IDs
                foreach ($order_diff as $salesId) {
                    SendProcessingOrder::dispatch($salesId, $company->url);
                }
            }
        }
    }
}
