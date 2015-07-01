<?php

/**
 * @category HotelNowVegas
 * @package RatesShellTest.php
 *
 * @author David Yell <neon1024@gmail.com>
 * @when 01/07/15
 *
 */

namespace CurrencyExchange\Tests\Shell;

use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOutput;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use CurrencyExchange\Shell\RatesShell;

class TestOutput extends ConsoleOutput
{
    public $output = '';

    protected function _write($message)
    {
        $this->output .= $message;
    }
}

class RatesShellTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->out = new TestOutput();
        $this->io = new ConsoleIo($this->out);
    }

    public function tearDown()
    {
        unset($this->out);
        unset($this->io);
    }

    public function testUpdate()
    {
        Configure::write('currencyLayer.apikey', 'exampleapikey');
        $jsonFixture = Plugin::path('CurrencyExchange') . 'tests' . DS . 'Fixtures' . DS . 'usd-api-response.json';

        $response = $this->getMockBuilder('\Cake\Network\Http\Response')
            ->setConstructorArgs([[], file_get_contents($jsonFixture)])
            ->setMethods(['isOk'])
            ->getMock();

        $response->code = 200;

        $response->expects($this->once())
            ->method('isOk')
            ->willReturn(true);

        $client = $this->getMockBuilder('\Cake\Network\Http\Client')
            ->setMethods(['get'])
            ->getMock();

        $client->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $shell = $this->getMockBuilder('CurrencyExchange\Shell\RatesShell')
            ->setConstructorArgs([$this->io])
            ->setMethods(['in', 'err', '_stop', 'clear', '_getClient'])
            ->getMock();

        $shell->expects($this->once())
            ->method('_getClient')
            ->willReturn($client);

        $shell->update();

        $this->assertRegExp("/Attempting to fetch the latest exchange rate data/", $this->out->output);
        $this->assertRegExp("/Response received `200`/", $this->out->output);
        $this->assertRegExp("/Exchange rate cache data updated/", $this->out->output);
    }

    public function testUpdateWithBadResponse()
    {
        Configure::write('currencyLayer.apikey', 'exampleapikey');
        $jsonFixture = Plugin::path('CurrencyExchange') . 'tests' . DS . 'Fixtures' . DS . 'usd-api-response.json';

        $response = $this->getMockBuilder('\Cake\Network\Http\Response')
            ->setConstructorArgs([[], file_get_contents($jsonFixture)])
            ->setMethods(['isOk'])
            ->getMock();

        $response->code = 503;

        $response->expects($this->once())
            ->method('isOk')
            ->willReturn(false);

        $client = $this->getMockBuilder('\Cake\Network\Http\Client')
            ->setMethods(['get'])
            ->getMock();

        $client->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $shell = $this->getMockBuilder('CurrencyExchange\Shell\RatesShell')
            ->setConstructorArgs([$this->io])
            ->setMethods(['in', 'err', '_stop', 'clear', '_getClient'])
            ->getMock();

        $shell->expects($this->once())
            ->method('_getClient')
            ->willReturn($client);

        $shell->update();

        $this->assertNotRegExp("/Response received `200`/", $this->out->output);
    }

    public function testUpdateWithNoApiKey()
    {
        Configure::write('currencyLayer.apikey', 'exampleapikey');
        $jsonFixture = Plugin::path('CurrencyExchange') . 'tests' . DS . 'Fixtures' . DS . 'no-api-key.json';

        $response = $this->getMockBuilder('\Cake\Network\Http\Response')
            ->setConstructorArgs([[], file_get_contents($jsonFixture)])
            ->setMethods(['isOk'])
            ->getMock();

        $response->code = 200;

        $response->expects($this->once())
            ->method('isOk')
            ->willReturn(true);

        $client = $this->getMockBuilder('\Cake\Network\Http\Client')
            ->setMethods(['get'])
            ->getMock();

        $client->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $shell = $this->getMockBuilder('CurrencyExchange\Shell\RatesShell')
            ->setConstructorArgs([$this->io])
            ->setMethods(['in', 'err', '_stop', 'clear', '_getClient'])
            ->getMock();

        $shell->expects($this->once())
            ->method('_getClient')
            ->willReturn($client);

        $shell->update();

        $this->assertRegExp("/You have not supplied an API Access Key/", $this->out->output);
    }

    public function testNoApiKeyConfigured()
    {
        Configure::write('currencyLayer.apikey', null);
        $shell = new RatesShell($this->io);
        $shell->update();

        $this->assertRegExp("/Please configure your CurrencyLayer API key in your application/", $this->out->output);
    }

    public function testBadCacheSave()
    {
        Configure::write('currencyLayer.apikey', 'exampleapikey');
        $jsonFixture = Plugin::path('CurrencyExchange') . 'tests' . DS . 'Fixtures' . DS . 'usd-api-response.json';

        $response = $this->getMockBuilder('\Cake\Network\Http\Response')
            ->setConstructorArgs([[], file_get_contents($jsonFixture)])
            ->setMethods(['isOk'])
            ->getMock();

        $response->code = 200;

        $response->expects($this->once())
            ->method('isOk')
            ->willReturn(true);

        $client = $this->getMockBuilder('\Cake\Network\Http\Client')
            ->setMethods(['get'])
            ->getMock();

        $client->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $shell = $this->getMockBuilder('CurrencyExchange\Shell\RatesShell')
            ->setConstructorArgs([$this->io])
            ->setMethods(['in', 'err', '_stop', 'clear', '_getClient', '_saveRates'])
            ->getMock();

        $shell->expects($this->once())
            ->method('_getClient')
            ->willReturn($client);

        $shell->expects($this->once())
            ->method('_saveRates')
            ->willReturn(false);

        $shell->update();

        $this->assertRegExp("/Cache could not be updated/", $this->out->output);
    }
}
