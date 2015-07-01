<?php
namespace CurrencyExchange\View\Helper;

use BadMethodCallException;
use Cake\Cache\Cache;
use Cake\View\Helper;
use Cake\View\View;

/**
 * CurrencyHelper
 *
 * @author David Yell <neon1024@gmail.com>
 */
class CurrencyHelper extends Helper {

    /**
     * Load other helpers that we need
     *
     * @var array
     */
	public $helpers = ['Number'];

    /**
     * Store the processed exchange rate data
     *
     * @var array
     */
	protected $exchangeRates;

    /**
     * The default settings for the helper
     *
     * @var array $settings Default configuration
     */
    public $_defaultConfig = [
        'sourceCurrency' => 'USD',
        'targetCurrency' => 'GBP'
    ];

    /**
     * Display the price in configured currency
     *
     * @param int $value
     * @param string $currencyCode A three letter currency code list of codes, http://en.wikipedia.org/wiki/ISO_4217#Active_codes
     * @return string
     * @throws BadMethodCallException
     */
	public function display($value, $currencyCode)
    {
		if (strlen($currencyCode) !== 3 || !is_string($currencyCode)) {
			throw new BadMethodCallException('Please pass a valid three letter currency code, such as GBP or USD.');
		}

        $rates = Cache::read('exchangeRateData', 'CurrencyExchange_ratesCache');
        if ($rates === false) {
            return;
        }
		
		if ($currencyCode === $this->settings['targetCurrency']) {
			return;
		}

		$amount = $value / $rates['quotes']->{$this->config('sourceCurrency') . $currencyCode};
		
		return $this->Number->currency($amount, $this->config('targetCurrency'));
	}
}