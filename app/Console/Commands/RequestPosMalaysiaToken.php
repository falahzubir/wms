<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ShippingApiController;
use App\Models\AccessToken;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
        $posmalaysia_tokens = AccessToken::where('type', 'posmalaysia');
        $posmalaysia_tokens = $posmalaysia_tokens->get();
        $posmalaysia_tokens_url = config('app.env') == 'production' ? 'https://posapi.pos.com.my/oauth2/token' : 'https://api-dev.pos.com.my/oauth2/token';

        foreach ($posmalaysia_tokens as $token) {
            try {
                $response = Http::asForm()
                    ->post($posmalaysia_tokens_url, [
                        'client_id' => config('app.env') == 'production' ? 'cea0dd40-67d0-4a70-a1a8-9ede1a42085a' : 'b3d254f7-4b9f-4dd7-b86c-bc5aa9eb898d',
                        'client_secret' => config('app.env') == 'production' ? 'cfc648c9-50a6-4780-acae-63550c5bc597' : '2ef405f9-55eb-4c01-ae02-b72d1ffab343',
                        'grant_type' => 'client_credentials',
                        // 'scope' => 'as2corporate.v2trackntracewebapijson.all as2corporate.tracking-event-list.all as2corporate.tracking-office-list.all as2corporate.tracking-reason-list.all as2poslaju.poslaju-poscode-coverage.all as01.gen-connote.all as01.generate-pl9-with-connote.all as2corporate.preacceptancessingle.all',
                    ]);

                if ($response->successful()) {
                    $data['token'] = $response['access_token'];
                    $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . $response['expires_in'] . ' seconds'));
                    $token->update($data);
                } else {
                    echo 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->reason();
                }
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        // dump('Pos Malaysia Token Cron Job Run successfully!');
        return Command::SUCCESS;
    }
}
