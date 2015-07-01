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
     * The remote api to grab exchange rate data from
     *
     * @var string
     */
    protected $ratesApi = 'http://www.getexchangerates.com/api/latest.json';

    /**
     * The default settings for the helper
     *
     * @var array $settings Default configuration
     */
    public $settings = [
        'targetCurrency' => 'GBP'
    ];

    /**
     * Build the helper
     *
     * @param View $View
     * @param array $settings
     */
    public function __construct(View $View, $settings = array())
    {
        parent::__construct($View, $settings);

        $this->exchangeRates = $this->getRates();
    }

    /**
     * Display the price in GBP
     *
     * @param int $value
     * @param string $currencyCode A three letter currency code
     *        list of codes, http://en.wikipedia.org/wiki/ISO_4217#Active_codes
     * @return string
     * @throws BadMethodCallException
     */
	public function display($value, $currencyCode)
    {
		if (strlen($currencyCode) !== 3 || !is_string($currencyCode)) {
			throw new BadMethodCallException('Please pass a valid three letter currency code, such as GBP or USD.');
		}
		
		if ($currencyCode === $this->settings['targetCurrency']) {
			return;
		}

        if ($this->exchangeRates === false) {
            return;
        }

		$amount = $value / $this->exchangeRates[$currencyCode];
		
		return $this->Number->currency($amount, $this->settings['targetCurrency']);
	}

    /**
     * Load exchange rates from either the cache or the remote api
     *
     * @return array
     */
	protected function getRates()
    {
        return Cache::read('exchangeRateData', 'CurrencyExchange.ratesCache');
	}
}