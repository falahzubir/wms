<?php

if(!defined('IS_ACTIVE')) define('IS_ACTIVE', 1);
if(!defined('IS_INACTIVE')) define('IS_INACTIVE', 0);

// paginate limit
if(!defined('PAGINATE_LIMIT')) define('PAGINATE_LIMIT', 50);
if(!defined('ORDER_NUMBER_LENGTH')) define('ORDER_NUMBER_LENGTH', '09'); // must have leading zero

// order status
if(!defined('ORDER_STATUS_PENDING')) define('ORDER_STATUS_PENDING', 1);
if(!defined('ORDER_STATUS_PROCESSING')) define('ORDER_STATUS_PROCESSING', 2);
if(!defined('ORDER_STATUS_PACKING')) define('ORDER_STATUS_PACKING', 3);
if(!defined('ORDER_STATUS_READY_TO_SHIP')) define('ORDER_STATUS_READY_TO_SHIP', 4);
if(!defined('ORDER_STATUS_SHIPPING')) define('ORDER_STATUS_SHIPPING', 5);
if(!defined('ORDER_STATUS_DELIVERED')) define('ORDER_STATUS_DELIVERED', 6);
if(!defined('ORDER_STATUS_RETURN_PENDING')) define('ORDER_STATUS_RETURN_PENDING', 7);
if(!defined('ORDER_STATUS_RETURN_SHIPPING')) define('ORDER_STATUS_RETURN_SHIPPING', 8);
if(!defined('ORDER_STATUS_RETURNED')) define('ORDER_STATUS_RETURNED', 9);
if(!defined('ORDER_STATUS_REJECTED')) define('ORDER_STATUS_REJECTED', 10);

// states
if(!defined('MY_STATES')) define('MY_STATES', [
    1 => 'Perlis',
    2 => 'Pulau Pinang',
    3 => 'Kedah',
    4 => 'Perak',
    5 => 'Kelantan',
    6 => 'Terengganu',
    7 => 'Pahang',
    8 => 'Selangor',
    9 => 'Negeri Sembilan',
    10 => 'Melaka',
    11 => 'Johor',
    12 => 'Sabah',
    13 => 'Sarawak',
    14 => 'Labuan',
    15 => 'Kuala Lumpur',
    16 => 'Putrajaya',
]);

// countries
if(!defined('COUNTRIES')) define('COUNTRIES', [
    'MY' => 'Malaysia',
]);

// actions
if(!defined('ACTION_ADD_TO_BUCKET')) define('ACTION_ADD_TO_BUCKET', 'add-to-bucket');
if(!defined('ACTION_GENERATE_CN')) define('ACTION_GENERATE_CN', 'generate-cn');
if(!defined('ACTION_DOWNLOAD_CN')) define('ACTION_DOWNLOAD_CN', 'download-cn');
if(!defined('ACTION_DOWNLOAD_ORDER')) define('ACTION_DOWNLOAD_ORDER', 'download-order');
if(!defined('ACTION_GENERATE_PICKING')) define('ACTION_GENERATE_PICKING', 'generate-pl');
if(!defined('ACTION_UPLOAD_TRACKING_BULK')) define('ACTION_UPLOAD_TRACKING_BULK', 'upload-tracking-bulk');
if(!defined('ACTION_APPROVE_AS_SHIPPED')) define('ACTION_APPROVE_AS_SHIPPED', 'approve-as-shipped');

// box minimum quantity
if(!defined('BOX_MINIMUM_QUANTITY')) define('BOX_MINIMUM_QUANTITY', 5);

// purchase type
if(!defined('PURCHASE_TYPE_COD')) define('PURCHASE_TYPE_COD', 1);
if(!defined('PURCHASE_TYPE_PAID')) define('PURCHASE_TYPE_PAID', 2);
if(!defined('PURCHASE_TYPE_INSTALLMENT')) define('PURCHASE_TYPE_INSTALLMENT', 3);

// Customer Type
if(!defined('CUSTOMER_TYPE_LEAD')) define('CUSTOMER_TYPE_LEAD', 1);
if(!defined('CUSTOMER_TYPE_PROSPECT')) define('CUSTOMER_TYPE_PROSPECT', 2);
if(!defined('CUSTOMER_TYPE_FOLLOWUP')) define('CUSTOMER_TYPE_FOLLOWUP', 3);
if(!defined('CUSTOMER_TYPE_BUYER')) define('CUSTOMER_TYPE_BUYER', 4);
if(!defined('CUSTOMER_TYPE_DEBTOR')) define('CUSTOMER_TYPE_DEBTOR', 5);

// order filters
if(!defined('ORDER_FILTER_COURIER')) define('ORDER_FILTER_COURIER', 1);
if(!defined('ORDER_FILTER_PURCHASE_TYPE')) define('ORDER_FILTER_PURCHASE_TYPE', 2);
if(!defined('ORDER_FILTER_TEAM')) define('ORDER_FILTER_TEAM', 3);
if(!defined('ORDER_FILTER_CUSTOMER_TYPE')) define('ORDER_FILTER_CUSTOMER_TYPE', 4);
if(!defined('ORDER_FILTER_OP_MODEL')) define('ORDER_FILTER_OP_MODEL', 5);
if(!defined('ORDER_FILTER_PRODUCT')) define('ORDER_FILTER_PRODUCT', 6);
if(!defined('ORDER_FILTER_SALES_EVENT')) define('ORDER_FILTER_SALES_EVENT', 7);
if(!defined('ORDER_FILTER_COMPANY')) define('ORDER_FILTER_COMPANY', 8);
if(!defined('ORDER_FILTER_STATE')) define('ORDER_FILTER_STATE', 9);

// date types
if(!defined('ORDER_DATE_TYPES')) define('ORDER_DATE_TYPES', [
    1 => ['Order Added', ''],
    2 => ['Request Shipping', ''],
    3 => ['Scan Parcel', ''],
    4 => ['Shipping', 'disabled'],
]);

//DHL
if(!defined('DHL_ID')) define('DHL_ID',15);

// courier id which will be auto shipping, other will have to set menually
if(!defined('AUTO_SHIPPING_COURIER')) define('AUTO_SHIPPING_COURIER', [15]);

// DHL EH
if(!defined('DHL_SOLD_PICKUP_ACCT')) define('DHL_SOLD_PICKUP_ACCT',[
    1=>'5265434590',//5265434590', // EH
    2=>'5286430910',//5286430910', // ED
    3=>'5999999885',//5264574522', // QA
]);
if(!defined('DHL_PREFIX')) define('DHL_PREFIX',[
    1=>'MY', // EH // MYCKZ
    2=>'MY', // ED // MYGPK
    3=>'MY', // QA
]);
if(!defined('DHL_CLIENT_ID')) define('DHL_CLIENT_ID','LTE2MDAwOTg0NTI=');
if(!defined('DHL_CLIENT_PASS')) define('DHL_CLIENT_PASS','MjAzMDI5MTU');

#32 DHL ED
if(!defined('DHL_ED_CLIENT_ID')) define('DHL_ED_CLIENT_ID','LTQ3NTc1NTQ1Mw==');
if(!defined('DHL_ED_CLIENT_PASS')) define('DHL_ED_CLIENT_PASS','MTM0MjQzM0208211627885710');

// MAXIMUM QUANTITY PER BOX
if(!defined('MAXIMUM_QUANTITY_PER_BOX')) define('MAXIMUM_QUANTITY_PER_BOX', 40);
if(!defined('MAX_DHL_COD_PER_PARCEL')) define('MAX_DHL_COD_PER_PARCEL', 200000); // RM2000

// Courier Others
if(!defined('COURIER_OTHERS')) define('COURIER_OTHERS', 99);

// Payment Type
if(!defined('PAYMENT_TYPE_SHOPEE')) define('PAYMENT_TYPE_SHOPEE', 22);

// auto reject dhl option
if(!defined('AUTO_REJECT_DHL')) define('AUTO_REJECT_DHL', true);

//blast id
if(!defined('OP_BLAST_ID')) define('OP_BLAST_ID', 16);

if(!defined('PROD_STORAGE_COND')) define('PROD_STORAGE_COND', [
    1 => 'Ambient',
    2 => 'Air-condition',
    3 => 'Chill',
    4 => 'Frozen',
]);
