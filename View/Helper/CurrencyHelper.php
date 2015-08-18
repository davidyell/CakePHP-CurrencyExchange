<?php
App::uses('HttpSocket', 'Network/Http');

/**
 * CurrencyHelper
 *
 * @author David Yell <neon1024@gmail.com>
 */
class CurrencyHelper extends AppHelper {

/**
 * Load other helpers that we need
 *
 * @var array
 */
	public $helpers = ['Number'];

/**
 * The default settings for the helper
 *
 * @var array $settings Default configuration
 */
    public $settings = [
        'sourceCurrency' => 'USD',
		'displayDefault' => true
    ];

	public function __construct(View $View, $settings = array())
	{
		$settings = array_merge($this->settings, $settings);
		parent::__construct($View, $settings);
	}

/**
 * Display the price in configured currency
 *
 * Due to the free API not allowing changes to the source currency, non-USD source currencies are converted to USD
 * and then to the target currency.
 *
 * @param int $value The monetary amount
 * @param string $currencyCode A three letter currency code list of codes, http://en.wikipedia.org/wiki/ISO_4217#Active_codes
 * @return void|string
 * @throws InvalidArgumentException
 */
	public function display($value, $currencyCode) {
		if (strlen($currencyCode) !== 3 || !is_string($currencyCode)) {
			throw new InvalidArgumentException('Please pass a valid three letter currency code, such as GBP or USD. Instead of ' . $currencyCode);
		}
		$currencyCode = strtoupper($currencyCode);

		$rates = $this->_getRates();
		if ($rates === false) {
			return;
		}

		if ($currencyCode === $this->settings['sourceCurrency']) {
			if ($this->settings['displayDefault'] === true) {
				return $this->Number->currency($value, $currencyCode);
			} else {
				return;
			}
		}

		if ($this->settings['sourceCurrency'] !== 'USD') {
			$usdValue = $value / $rates['quotes']['USD' . $this->settings['sourceCurrency']];
			if ($currencyCode === 'USD') {
				$amount = $usdValue;
			} else {
				$amount = $usdValue * $rates['quotes']['USD' . $currencyCode];
			}
		} else {
			$amount = $value * $rates['quotes'][$this->settings['sourceCurrency'] . $currencyCode];
		}
		return $this->Number->currency($amount, $currencyCode);
	}

/**
 * Load exchange rates from either the cache or the remote api
 *
 * @return array
 */
	protected function _getRates() {
        return Cache::read('exchangeRateData', 'CurrencyExchange_ratesCache');
	}
}