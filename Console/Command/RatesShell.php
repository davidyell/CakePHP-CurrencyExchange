<?php

/**
 * @category CurrencyExchange
 * @package RatesShell.php
 * 
 * @author David Yell <neon1024@gmail.com>
 * @when 26/03/15
 *
 */

App::uses('HttpSocket', 'Network/Http');

class RatesShell extends AppShell {

    protected $ratesApi = 'http://www.getexchangerates.com/api/latest.json';

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('update', [
            'help' => __('Try and update the Exchange Rate cached data from the remote api.'),
            'parser' => [
                'description' => [__('Attempt to fetch updated data from the remote api and cache it locally.')],
                'arguments' => [
                    'currency' => [
                        'help' => __('The target currency code, such as GBP or USD.'),
                        'required' => true
                    ],
                ]
            ]
        ]);

        return $parser;
    }

/**
 * Update the local cache with data from the remote api
 *
 * @return array|bool
 */
    public function update() {
        $this->out(__('<info>Attempting to fetch the latest exchange rate data..</info>'));

        $http = new HttpSocket();

        /* @var HttpSocketResponse $response */
        $response = $http->get($this->ratesApi, ['base' => $this->args[0]]);

        if ($response->isOk()) {
            $this->out(__("Response received `{$response->code}`"));

            $json = json_decode($response->body);
            $rates = (array)$json;

            if (is_array($rates) && !empty($rates)) {
                Cache::write('exchangeRateData', $rates, 'ratesCache');
                $this->out(__("Exchange rate cache data updated."));
                $this->out(__("New timestamp is `" . date('d M Y H:i:s', (int)$rates['DateTime']) . "`"));
            }
        } else {
            $this->out(__("<error>Response received `{$response->code}`</error>"));
        }
    }
}