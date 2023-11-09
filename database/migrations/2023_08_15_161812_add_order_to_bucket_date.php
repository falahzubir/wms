<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          //check if column exists
          if (Schema::hasColumn('orders', 'bucket_added_at')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('bucket_added_at')->nullable()->after('bucket_id');
        });

        // populate bucket_added_at from order log timestamp with status 2 (latest)
        $orders = Order::active()->with('logs')->get();

        foreach ($orders as $order) {
            $bucket_added_at = $order->logs->where('order_status_id', 2)->where('remarks', 'Order added to bucket')->sortByDesc('id')->first();
            if($bucket_added_at) {
                Order::where('id', $order->id)->update(['bucket_added_at' => $bucket_added_at->created_at->format('Y-m-d H:i:s')]);
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('bucket_added_at');
        });
    }
};
