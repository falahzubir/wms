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

        foreach ($posmalaysia_tokens as $token) {
            try {
                $response = Http::asForm()
                    ->post('https://gateway-usc.pos.com.my/security/connect/token', [
                        'client_id' => '64dda61cfa1b1b000ed9fb30',
                        'client_secret' => 'cpy70tObJYUXa+67Wtw4+nQ44JCcCKkowXN5RV/sIgE=',
                        'grant_type' => 'client_credentials',
                        'scope' => 'as2corporate.v2trackntracewebapijson.all as2corporate.tracking-event-list.all as2corporate.tracking-office-list.all as2corporate.tracking-reason-list.all as2poslaju.poslaju-poscode-coverage.all as01.gen-connote.all as01.generate-pl9-with-connote.all as2corporate.preacceptancessingle.all',
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

        dump('Pos Malaysia Token Cron Job Run successfully!');
        return Command::SUCCESS;
    }
}
