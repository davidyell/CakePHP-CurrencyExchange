<?php
use Cake\Core\Plugin;

require dirname(dirname(__FILE__)) . '/vendor/cakephp/cakephp/tests/bootstrap.php';
require dirname(dirname(__FILE__)) . '/config/bootstrap.php';

Plugin::load('CurrencyExchange', ['path' => dirname(dirname(__FILE__)) . DS]);
