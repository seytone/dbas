<?php

return [
   'transunion'   => [
      'url'                => env('TRANSUNION_URL'),
      'uid'                => env('TRANSUNION_UID'),
      'pwd'                => env('TRANSUNION_PWD'),
      'wsdl'               => env('TRANSUNION_WSDL'),
      'Channel'            => env('TRANSUNION_CHANNEL'),
      'Product'            => env('TRANSUNION_PRODUCT'),
      'SolutionSetId'      => env('TRANSUNION_SOLUSETID'),
      'SolutionSetVersion' => env('TRANSUNION_SOLUSETVER'),
   ],
   'curl'        => [
      'ssl_verify'         => env('CURL_SSL_VERIFY', true),
      'connection_timeout' => env('CURL_CONNECTTIMEOUT', 10),
      'timeout'            => env('CURL_TIMEOUT', 30),
      'debug'              => env('CURL_DEBUG', false),
   ]
];
