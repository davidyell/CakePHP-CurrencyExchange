<?php

/**
 * Create a cache config to store the exchange rate data
 */
Cache::config('CurrencyExchange.ratesCache', array(
    'engine' => 'File',
    'path' => TMP . 'cache' . DS,
    'duration' => '+1 year',
    'probability' => 100,
    'prefix' => 'currency_exchange_',
    'persistent' => true,
    'compress' => false
));