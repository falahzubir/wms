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
    protected $company_url;

    /**
     * Create a new job instance.
     * @param mixed $sales_id Sales ID to process.
     * @param mixed $company Company to process.
     */
    public function __construct($sales_id, $company_url)
    {
        $this->sales_id = $sales_id;
        $this->company_url = $company_url;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // $company = Company::find($this->company_id);
            // $response = Http::get("{$this->company_url}/wms/send_sales/{$this->sales_id}");
            shell_exec("curl {$this->company_url}/wms/send_sales/{$this->sales_id}");
            Log::info("Processing order {$this->sales_id} for company {$this->company_url}");
    }
}
