<?php

return [

'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => [
    'https://appsuaregiao.com.br',
    'https://www.appsuaregiao.com.br',
    'http://localhost:5173',
    'http://127.0.0.1:5173',
],
'allowed_headers' => ['*'],
'supports_credentials' => true,

];
