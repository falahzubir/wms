<?php

if(!defined('IS_ACTIVE')) define('IS_ACTIVE', 1);
if(!defined('IS_INACTIVE')) define('IS_INACTIVE', 1);

// paginate limit
if(!defined('PAGINATE_LIMIT')) define('PAGINATE_LIMIT', 10);
if(!defined('ORDER_NUMBER_LENGTH')) define('ORDER_NUMBER_LENGTH', '09'); // must have leading zero

// order status
if(!defined('ORDER_STATUS_PENDING')) define('ORDER_STATUS_PENDING', 1);
if(!defined('ORDER_STATUS_PROCESSING')) define('ORDER_STATUS_PROCESSING', 2);
if(!defined('ORDER_STATUS_PACKING')) define('ORDER_STATUS_PACKING', 3);
if(!defined('ORDER_STATUS_READY_TO_SHIP')) define('ORDER_STATUS_READY_TO_SHIP', 4);
if(!defined('ORDER_STATUS_SHIPPING')) define('ORDER_STATUS_SHIPPING', 5);
if(!defined('ORDER_STATUS_DELIVERED')) define('ORDER_STATUS_DELIVERED', 6);
if(!defined('ORDER_STATUS_COMPLETED')) define('ORDER_STATUS_RETURN_PENDING', 7);
if(!defined('ORDER_STATUS_COMPLETED')) define('ORDER_STATUS_RETURN_SHIPPING', 8);
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

// actions
if(!defined('ACTION_ADD_TO_BUCKET')) define('ACTION_ADD_TO_BUCKET', 'add-to-bucket');
if(!defined('ACTION_GENERATE_CN')) define('ACTION_GENERATE_CN', 'generate-cn');
if(!defined('ACTION_DOWNLOAD_CN')) define('ACTION_DOWNLOAD_CN', 'download-cn');
if(!defined('ACTION_DOWNLOAD_ORDER')) define('ACTION_DOWNLOAD_ORDER', 'download-order');
if(!defined('ACTION_GENERATE_PICKING')) define('ACTION_GENERATE_PICKING', 'generate-pl');
if(!defined('ACTION_UPLOAD_TRACKING_BULK')) define('ACTION_UPLOAD_TRACKING_BULK', 'upload-tracking-bulk');

// box minimum quantity
if(!defined('BOX_MINIMUM_QUANTITY')) define('BOX_MINIMUM_QUANTITY', 4);

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
