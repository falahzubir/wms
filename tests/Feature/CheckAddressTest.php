<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckAddressTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_address()
    {
        // $this->assertTrue(true);
        $order = new \App\Http\Controllers\OrderController();
        $response = $order->check_duplicate(\App\Models\Customer::find(1), \App\Models\Order::find(1));
        dump($response);


        // $response->assertStatus(200);
    }
}
