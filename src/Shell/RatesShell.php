<?php

namespace CurrencyExchange\Shell;

/**
 * @category CurrencyExchange
 * @package RatesShell.php
 *
 * @author David Yell <neon1024@gmail.com>
 * @when 26/03/15
 */

use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Network\Http\Client;

class RatesShell extends Shell
{
    /**
     * Api endpoint url
     *
     * @var string
     */
    protected $ratesApi = 'http://apilayer.net/api/live';

    /**
     * Output the available console commands and options
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
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
     * Output the help options when using the shell without a command
     *
     * @return void
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }

    /**
     * Update the local cache with data from the remote api
     *
     * @return void
     */
    public function update()
    {
        $this->out(__('<info>Attempting to fetch the latest exchange rate data..</info>'));

        if (Configure::read('currencyLayer.apikey') === null) {
            $this->out(__("<error>Please configure your CurrencyLayer API key in your application. Using the 'currencyLayer.apikey' config key.</error>"));
            return;
        }

        $http = $this->_getClient();

        /* @var \Cake\Network\Http\Response $response */
        $response = $http->get($this->ratesApi, [
            'source' => $this->param('currency'),
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
                if ($this->_saveRates($rates)) {
                    $this->out(__("Exchange rate cache data updated."));
                    $this->out(__("New timestamp is `" . date('d M Y H:i:s', $rates['timestamp']) . "`"));
                } else {
                    $this->out(__("<error>Cache could not be updated.</error>"));
                }
            }
        } else {
            $this->out(__("<error>Response received `{$response->code}`</error>"));
        }
    }

    /**
     * Wrapper method for testing
     *
     * @return \Cake\Network\Http\Client
     */
    protected function _getClient()
    {
        return new Client();
    }

    /**
     * Save the current exchange rate data into cache
     *
     * @param array $rates Array return
     * @return bool
     */
    protected function _saveRates($rates)
    {
        return Cache::write('exchangeRateData', $rates, 'CurrencyExchange_ratesCache');
    }
}
