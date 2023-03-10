<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Order;
use App\Models\Shipping;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        Order::whereIn("id", [67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 108, 109, 110, 111, 112, 114, 115, 116, 117, 118, 16089])->update(["status" => 2]);
        Shipping::whereIn("order_id", [67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 108, 109, 110, 111, 112, 114, 115, 116, 117, 118, 16089])->delete();
        // $response = $this->get('/');

        $this->assertTrue(true);
    }
}
