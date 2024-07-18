<?php

namespace App\Jobs;

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

    /**
     * Create a new job instance.
     * @param mixed $sales_id Sales ID to process.
     */
    public function __construct($sales_id)
    {
        $this->sales_id = $sales_id;
    }

    /**
     * Execute the job.
     */
       public function handle()
    {
       $response = Http::post("https://bosemzi.com/wms/send_sales/{$this->sales_id}");

      // Check if the request was successful
      if ($response->successful()) {
        // Log success
        Log::info("Sales ID {$this->sales_id} sent to BOS successfully.");
       } else {
        // Log failure along with the response body for debugging
        Log::error("Failed to send Sales ID {$this->sales_id} to BOS. Response: " . $response->body());
     }
   }
}
