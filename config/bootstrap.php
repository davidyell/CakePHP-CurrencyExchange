<?php

/**
 * Create a cache config to store the exchange rate data
 */
\Cake\Cache\Cache::config('CurrencyExchange_ratesCache', [
    'className' => 'File',
    'path' => CACHE,
    'duration' => '+1 year',
    'prefix' => 'currency_exchange_'
]);
