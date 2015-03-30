<?php

/**
 * Create a cache config to store the exchange rate data
 */
Cache::config('ratesCache', array(
    'engine' => 'File',
    'duration' => '+1 year',
    'probability' => 100,
    'prefix' => 'currency_exchange_',
    'persistent' => true,
    'compress' => false,
));