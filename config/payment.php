<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Casso Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Casso là dịch vụ webhook ngân hàng tự động của Việt Nam.
    | Đăng ký tại https://casso.vn để lấy API Key.
    |
    */

    'casso_api_key'     => env('CASS_API_KEY', ''),
    'casso_bank_name'   => env('CASS_BANK_NAME', 'MB Bank'),
    'casso_account_no'  => env('CASS_ACCOUNT_NO', ''),
    'casso_account_name'=> env('CASS_ACCOUNT_NAME', 'Bookverse'),
];
