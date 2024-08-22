<?php
namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendProcessingOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
     
    protected $sales_id;
    protected $company;
    
    /**
     * Create a new job instance.
     * @param mixed $sales_id Sales ID to process.
     * @param mixed $company Company to process.
     */
    public function __construct($sales_id, $company)
    {
        $this->sales_id = $sales_id;
        $this->company = $company;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // $company = Company::find($this->company_id);
            $response = Http::post("{$this->company->url}/wms/send_sales/{$this->sales_id}");

            if ($response->successful()) {
                // Log success
                Log::info("Sales ID {$this->sales_id} sent to {$this->company->url} successfully.");
            } else {
                Log::error("Failed to send Sales ID {$this->sales_id} to BOS. Response: " . $response->body());
            }
        
    }
}