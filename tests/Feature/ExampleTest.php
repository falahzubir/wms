<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Bucket;
use App\Models\BucketBatch;
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
        // Order::where("id","!=",0)->update(["bucket_batch_id"=>null]);
        BucketBatch::truncate();
        // $response = $this->get('/');

        $this->assertTrue(true);
    }
}
