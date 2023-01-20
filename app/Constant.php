<?php

if(!defined('IS_ACTIVE')) define('IS_ACTIVE', 1);
if(!defined('IS_INACTIVE')) define('IS_INACTIVE', 1);

// paginate limit
if(!defined('PAGINATE_LIMIT')) define('PAGINATE_LIMIT', 10);
if(!defined('ORDER_NUMBER_LENGTH')) define('ORDER_NUMBER_LENGTH', '09');

// order status
if(!defined('ORDER_STATUS_PENDING')) define('ORDER_STATUS_PENDING', '1');
if(!defined('ORDER_STATUS_PENDING_ON_BUCKET')) define('ORDER_STATUS_PENDING_ON_BUCKET', '2');
if(!defined('ORDER_STATUS_PACKING')) define('ORDER_STATUS_PACKING', '3');
if(!defined('ORDER_STATUS_READY_TO_SHIP')) define('ORDER_STATUS_READY_TO_SHIP', '4');
if(!defined('ORDER_STATUS_SHIPPING')) define('ORDER_STATUS_SHIPPING', '5');
if(!defined('ORDER_STATUS_DELIVERED')) define('ORDER_STATUS_DELIVERED', '6');
if(!defined('ORDER_STATUS_RETURNED')) define('ORDER_STATUS_RETURNED', '7');
if(!defined('ORDER_STATUS_COMPLETED')) define('ORDER_STATUS_COMPLETED', '8');

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
if(!defined('ACTION_UPLOAD_TRACKING_BULK')) define('ACTION_UPLOAD_TRACKING_BULK', 'upload-tracking-bulk');
