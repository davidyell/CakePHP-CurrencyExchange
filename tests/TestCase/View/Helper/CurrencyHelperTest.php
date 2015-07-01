<?php

/**
 * @category HotelNowVegas
 * @package CurrencyHelperTest.php
 *
 * @author David Yell <neon1024@gmail.com>
 * @when 01/07/15
 *
 */

namespace CurrencyExchange\Tests\View\Helper;

use Cake\Core\Plugin;
use CurrencyExchange\View\Helper\CurrencyHelper;
use InvalidArgumentException;

class CurrencyHelperTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->View = $this->getMockBuilder('\Cake\View\View')
            ->setMethods(['loadHelpers'])
            ->getMock();
    }

    public function providerCurrencies()
    {
        return [
            [
                ['sourceCurrency' => 'USD'],
                '9.99',
                'GBP',
                '£6.38'
            ],
            [
                ['sourceCurrency' => 'USD'],
                '100',
                'GBP',
                '£63.88'
            ],
            [
                ['sourceCurrency' => 'USD'],
                '100',
                'JPY',
                '¥12,291'
            ],
            [
                ['sourceCurrency' => 'USD'],
                '100',
                'EUR',
                '€89.91'
            ],
            [
                ['sourceCurrency' => 'USD'],
                '100',
                'SLL',
                'SLL426,000'
            ],
            [
                ['sourceCurrency' => 'GBP'],
                '100',
                'USD',
                '$156.55'
            ],
            [
                ['sourceCurrency' => 'GBP'],
                '100',
                'JPY',
                '¥19,242'
            ],
        ];
    }

    /**
     * @dataProvider providerCurrencies
     * @param array $config The helper config
     * @param int|float $value The monetary value
     * @param string $code Three digit currency code
     * @param string $expected
     */
    public function testDisplayCurrency($config, $value, $code, $expected)
    {
        $helper = $this->getMockBuilder('CurrencyExchange\View\Helper\CurrencyHelper')
            ->setConstructorArgs([$this->View, $config])
            ->setMethods(['_getRates'])
            ->getMock();

        $jsonFixture = Plugin::path('CurrencyExchange') . 'tests' . DS . 'Fixtures' . DS . 'usd-api-response.json';

        $helper->expects($this->once())
            ->method('_getRates')
            ->willReturn(json_decode(file_get_contents($jsonFixture), true));

        $result = $helper->display($value, $code);
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadCurrencyCode()
    {
        $helper = new CurrencyHelper($this->View);
        $helper->display('9.95', 'BadCode');
    }

    public function testSourceSameAsTarget()
    {
        $helper = new CurrencyHelper($this->View);
        $result = $helper->display('9.99', 'USD');
        $this->assertEquals(null, $result);
    }

    public function testNoExchangeData()
    {
        $helper = $this->getMockBuilder('CurrencyExchange\View\Helper\CurrencyHelper')
            ->setConstructorArgs([$this->View])
            ->setMethods(['_getRates'])
            ->getMock();

        $helper->expects($this->once())
            ->method('_getRates')
            ->willReturn(false);

        $result = $helper->display('100', 'GBP');
        $this->assertEquals(null, $result);
    }
}
