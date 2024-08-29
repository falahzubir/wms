<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

Trait DownloadCsvTrait
{

    public static function query($header, $columnHeaders, $order_ids, $filename)
    {
        $data = [];
        $i = 1;
        $data[] = $header;
        $ordersSelect = '
            ROW_NUMBER() OVER () AS blank,
            IFNULL(orders.sales_id, "-") AS sales_id,
            IFNULL(orders.id, "-") AS order_id,
            CASE
                WHEN orders.purchase_type = 1 THEN "COD"
                WHEN orders.purchase_type = 2 THEN "Paid"
                WHEN orders.purchase_type = 3 THEN "Installment"
                ELSE "-"
            END AS purchase_type,
            IFNULL(operational_models.name, "-") AS operational_models_name,
            IFNULL(payment_types.payment_type_name, "-") AS payment_type_name,
            IFNULL(couriers.name, "-") AS couriers_name,
            IFNULL(TRUNCATE(orders.total_price / 100, 2), "0") AS total_price,
            IFNULL(TRUNCATE(orders.payment_refund / 100, 2), "0") AS payment_refund,
            IFNULL(orders.sales_remarks, "-") AS sales_remarks,
            order_items.quantity AS quantity,
            order_items.weight AS weight,
            order_items.product_code AS product_code,
            order_items.product_name AS product_name,
            IFNULL(orders.created_at, "-") AS date_insert,
            IFNULL(delivered_logs.created_at, "-") AS delivered_date,
        ';


        $companiesSelect = '
            IFNULL(companies.id, "") AS companies_id,
            IFNULL(companies.name, "-") AS companies_name,
            IFNULL(companies.phone, "-") AS companies_phone,
            IFNULL(companies.address, "-") AS companies_address,
            IFNULL(companies.postcode, "-") AS companies_postcode,
            IFNULL(companies.city, "-") AS companies_city,
            IFNULL(companies.state, "-") AS companies_state,
            IFNULL(companies.country, "-") AS companies_country,
        ';


        $customersSelect = '
            IFNULL(customers.name, "-") AS customers_name,
            IFNULL(customers.phone, "-") AS customers_phone,
            IFNULL(customers.phone_2, "-") AS customers_phone_2,
            IFNULL(customers.address, "-") AS customers_address,
            IFNULL(customers.postcode, "-") AS customers_postcode,
            IFNULL(customers.city, "-") AS customers_city,
            IFNULL(customers_states.name, "-") AS customers_state,
            CASE
                WHEN customers.country = 1 THEN "MY"
                WHEN customers.country = 2 THEN "ID"
                WHEN customers.country = 3 THEN "SG"
                ELSE "-"
            END AS customers_country,
            IFNULL(state_groups.list_name, "-") AS state_group,
        ';


        $shippingsSelect = "
            shippings.shipment_number AS shipping_number,
            IFNULL(shippings.created_at, '-') AS shipping_date,
            CASE
                WHEN shippings.tracking_number REGEXP '^[0-9]+$' THEN CONCAT(\"'\", shippings.tracking_number)
                ELSE IFNULL(shippings.tracking_number, '-')
            END AS tracking_number,
            IFNULL(shippings.scanned_at, '-') AS scan_date,
            IFNULL(shippings_users.name, '-') AS pic_scan,
            IFNULL(CONCAT(shippings.total_weight, 'g'), '-') AS total_weight,
            IFNULL(shipping_costs.weight_category_names, '-') AS weight_category,
            IFNULL(TRUNCATE(shipping_costs.price / 100, 2), '-') AS shipping_cost,
            IFNULL(shipping_products.quantity, '0') AS shipping_cost_product_quantity
        ";


        $select = $ordersSelect . $companiesSelect. $customersSelect . $shippingsSelect;

        $sql = DB::table('orders')
            ->leftJoin('shippings', function ($join) {
                $join->on('orders.id', '=', 'shippings.order_id')
                    ->where('shippings.status', 1);
            })
            ->leftJoin('companies', 'orders.company_id', '=', 'companies.id')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('states as customers_states', 'customers.state', '=', 'customers_states.id')
            ->leftJoin('operational_models', 'orders.operational_model_id', '=', 'operational_models.id')
            ->leftJoin('payment_types', 'orders.payment_type', '=', 'payment_types.id')
            ->leftJoin('couriers', 'orders.courier_id', '=', 'couriers.id')
            ->leftJoin(DB::raw('(SELECT order_items.order_id, GROUP_CONCAT(order_items.quantity) AS quantity, GROUP_CONCAT(products.weight) AS weight, GROUP_CONCAT(products.code) AS product_code, GROUP_CONCAT(products.name) AS product_name FROM order_items LEFT JOIN products ON products.id = order_items.product_id WHERE order_items.status = 1 GROUP BY order_id) AS order_items'), 'orders.id', '=', 'order_items.order_id')
            ->leftJoin(DB::raw('(SELECT group_state_lists.state_id, GROUP_CONCAT(state_groups.name) AS list_name FROM group_state_lists LEFT JOIN state_groups ON state_groups.id = group_state_lists.state_group_id WHERE group_state_lists.deleted_at IS NULL GROUP BY state_id) AS state_groups'), 'customers.state', '=', 'state_groups.state_id')
            ->leftJoin('users AS shippings_users', 'shippings.scanned_by', '=', 'shippings_users.id')
            ->leftJoin('order_logs AS delivered_logs', function ($join) {
                $join->on('orders.id', '=', 'delivered_logs.order_id')
                    ->where('delivered_logs.order_status_id', 6)
                    ->where('delivered_logs.status', 1);
            })
            ->leftJoin(DB::raw('(SELECT shipping_costs.id, shipping_costs.price, weight_categories.name AS weight_category_names FROM shipping_costs LEFT JOIN weight_categories ON weight_categories.id = shipping_costs.weight_category_id WHERE shipping_costs.deleted_at IS NULL GROUP BY shipping_costs.id) AS shipping_costs'), 'shippings.shipping_cost_id', '=', 'shipping_costs.id')
            ->leftJoin(DB::raw('(SELECT shipping_products.shipping_id, SUM(shipping_products.quantity) AS quantity FROM shipping_products GROUP BY shipping_products.shipping_id) AS shipping_products'), 'shipping_products.shipping_id', '=', 'shippings.id')
            ->selectRaw($select)
            ->whereIn('orders.id',  $order_ids)
            ->get();

        $results = $sql->toArray();

        $sortedColumns = $columnHeaders->templateColumns->sortBy('column_position');

        // Get order PIC from BOS
        $staffMain = self::get_order_pic(array_column($results, 'sales_id'), array_column($results, 'companies_id'));
        $sortedData = [];

        $i = 1;
        foreach ($results as $row)
        {
            $uniqueKey = $row->order_id . '_' . $i;
            foreach ($sortedColumns as $template)
            {
                $column = $template->columns->column_name;

                if ($column == 'blank') {
                    $sortedData[$uniqueKey][$column] = $row->blank;
                } elseif ($column == 'sales_id') {
                    $sortedData[$uniqueKey][$column] = $row->sales_id;
                } elseif ($column == 'order_id') {
                    $sortedData[$uniqueKey][$column] = $row->order_id;
                } elseif ($column == 'companies_name') {
                    $sortedData[$uniqueKey][$column] = $row->companies_name;
                } elseif ($column == 'companies_phone') {
                    $sortedData[$uniqueKey][$column] = $row->companies_phone;
                } elseif ($column == 'companies_address') {
                    $sortedData[$uniqueKey][$column] = $row->companies_address;
                } elseif ($column == 'companies_postcode') {
                    $sortedData[$uniqueKey][$column] = $row->companies_postcode;
                } elseif ($column == 'companies_city') {
                    $sortedData[$uniqueKey][$column] = $row->companies_city;
                } elseif ($column == 'companies_state') {
                    $sortedData[$uniqueKey][$column] = $row->companies_state;
                } elseif ($column == 'companies_country') {
                    $sortedData[$uniqueKey][$column] = $row->companies_country;
                } elseif ($column == 'customers_name') {
                    $sortedData[$uniqueKey][$column] = $row->customers_name;
                } elseif ($column == 'customers_phone') {
                    $sortedData[$uniqueKey][$column] = $row->customers_phone;
                } elseif ($column == 'customers_phone_2') {
                    $sortedData[$uniqueKey][$column] = $row->customers_phone_2;
                } elseif ($column == 'customers_address') {
                    $sortedData[$uniqueKey][$column] = $row->customers_address;
                } elseif ($column == 'customers_postcode') {
                    $sortedData[$uniqueKey][$column] = $row->customers_postcode;
                } elseif ($column == 'customers_city') {
                    $sortedData[$uniqueKey][$column] = $row->customers_city;
                } elseif ($column == 'customers_state') {
                    $sortedData[$uniqueKey][$column] = $row->customers_state;
                } elseif ($column == 'customers_country') {
                    $sortedData[$uniqueKey][$column] = $row->customers_country;
                } elseif ($column == 'purchase_type') {
                    $sortedData[$uniqueKey][$column] = $row->purchase_type;
                } elseif ($column == 'operational_models_name') {
                    $sortedData[$uniqueKey][$column] = $row->operational_models_name;
                } elseif ($column == 'payment_type_name') {
                    $sortedData[$uniqueKey][$column] = $row->payment_type_name;
                } elseif ($column == 'couriers_name') {
                    $sortedData[$uniqueKey][$column] = $row->couriers_name;
                } elseif ($column == 'total_price') {
                    $sortedData[$uniqueKey][$column] = $row->total_price;
                } elseif ($column == 'quantity') {
                    $sortedData[$uniqueKey][$column] = get_order_quantity_csv($row->quantity);
                } elseif ($column == 'weight') {
                    $sortedData[$uniqueKey][$column] =  get_order_weight_csv($row->weight);
                } elseif ($column == 'item_description') {
                    $sortedData[$uniqueKey][$column] = get_shipping_remarks_csv($row, false);
                } elseif ($column == 'date_insert') {
                    $sortedData[$uniqueKey][$column] = $row->date_insert;
                } elseif ($column == 'shipping_date') {
                    $sortedData[$uniqueKey][$column] = $row->shipping_date;
                } elseif ($column == 'tracking_number') {
                    $sortedData[$uniqueKey][$column] = $row->tracking_number;
                } elseif ($column == 'scan_date') {
                    $sortedData[$uniqueKey][$column] = $row->scan_date;
                } elseif ($column == 'pic_scan') {
                    $sortedData[$uniqueKey][$column] = $row->pic_scan;
                } elseif ($column == 'delivered_date') {
                    $sortedData[$uniqueKey][$column] = $row->delivered_date;
                } elseif ($column == 'order_pic') {
                    $salesId = $row->sales_id;
                    $staffNames = json_decode($staffMain, true);
                    $found = false;
                    if (!empty($staffNames)) {
                        foreach ($staffNames as $staff) {
                            if ($staff['sales_id'] == $salesId) {
                                $sortedData[$uniqueKey][$column] = $staff['staff_name'];
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        $sortedData[$uniqueKey][$column] = '-';
                    }
                } elseif ($column == 'state_group') {
                    $sortedData[$uniqueKey][$column] = $row->state_group;
                } elseif ($column == 'total_weight') {
                    $sortedData[$uniqueKey][$column] = $row->total_weight;
                } elseif ($column == 'weight_category') {
                    $sortedData[$uniqueKey][$column] = $row->weight_category;
                } elseif ($column == 'shipping_cost') {
                    $sortedData[$uniqueKey][$column] = $row->shipping_cost;
                } elseif ($column == 'shipping_number') {
                    $sortedData[$uniqueKey][$column] = $row->shipping_number;
                } elseif ($column == 'payment_refund') {
                    $sortedData[$uniqueKey][$column] = $row->payment_refund;
                } elseif ($column == 'sales_remark') {
                    $sortedData[$uniqueKey][$column] = $row->sales_remarks;
                } elseif ($column == 'shipping_remarks') {
                    $sortedData[$uniqueKey][$column] = get_shipping_remarks_csv($row, true);
                } elseif ($column == 'shipping_cost_product_quantity') {
                    $sortedData[$uniqueKey][$column] = $row->shipping_cost_product_quantity;
                } else {
                    $sortedData[$uniqueKey][$column] = $row->{$column} ?? '';
                }

            }
            $i++;
            $data[] = array_values($sortedData[$uniqueKey]);
        }

        return self::download($data, $filename);

    }

    public static function get_order_pic($salesIds, $companyId)
    {
        // Fetch company URL from the database
        $url = \App\Models\Company::where('id', $companyId)->value('url');
        $curl_url = $url . '/wms/get_staff_name';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            // CURLOPT_URL => 'http://localhost/bos/wms/get_staff_name', // Test
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true, // Return response as a string
            CURLOPT_HTTPHEADER => [ // Set headers if needed
                'Content-Type: application/json',
            ],
            CURLOPT_POST => true, // Use POST request
            CURLOPT_POSTFIELDS => json_encode(['sales_ids' => $salesIds]),
        ]);

        // Execute cURL session
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        return $response;
    }

    public static function download($data, $filename)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        // Assuming the first element of $data is the header
        $header = array_shift($data);

        $callback = function () use ($data, $header) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $header); // Write the header row
            foreach ($data as $row) {
                fputcsv($file, $row); // Write each data row
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
