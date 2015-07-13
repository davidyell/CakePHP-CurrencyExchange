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

    protected $ratesApi = 'http://apilayer.net/api/live';

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('update', [
            'help' => __('Try and update the Exchange Rate cached data from the remote api.'),
            'parser' => [
                'description' => [__('Attempt to fetch updated USD data from the remote api and cache it locally.')],
            ]
        ]);

        return $parser;
    }

    public function main() {
        $this->out($this->OptionParser->help());
    }

/**
 * Update the local cache with data from the remote api
 *
 * @return array|bool
 */
    public function update() {
        $this->out(__('<info>Attempting to fetch the latest exchange rate data..</info>'));

        if (Configure::read('currencyLayer.apikey') === null) {
            $this->out(__("<error>Please configure your CurrencyLayer API key in your application. Using the 'currencyLayer.apikey' config key.</error>"));
            return;
        }

        $http = new HttpSocket();

        /* @var HttpSocketResponse $response */
        $response = $http->get($this->ratesApi, [
            'access_key' => Configure::read('currencyLayer.apikey')
        ]);

        if ($response->isOk()) {
            $this->out(__("Response received `{$response->code}`"));

            $rates = json_decode($response->body, true);

            if (isset($rates['success']) && $rates['success'] == false) {
                $this->out(__("<error>[" . $rates['error']['code'] . ':' . $rates['error']['type'] . '] ' . $rates['error']['info'] . "</error>"));
                return;
            }

            if (is_array($rates) && !empty($rates)) {
                if (Cache::write('exchangeRateData', $rates, 'CurrencyExchange_ratesCache')) {
                    $this->out(__("Exchange rate cache data updated."));
                    $this->out(__("New timestamp is `" . date('d M Y H:i:s', (int)$rates['timestamp']) . "`"));
                } else {
                    $this->out(__("<error>Cache could not be updated.</error>"));
                }
            }
        } else {
            $this->out(__("<error>Response received `{$response->code}`</error>"));
        }
    }
}