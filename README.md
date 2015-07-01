# Currency Exchange
A helper for CakePHP 3 which will display a currency in a different currency using current exchange rates.

It will cache the result and check the response before invalidating the cache to ensure that your site always serves some currency data, even if it's a little stale. The data is cached for a year and updated using a shell.

## Requirements
* CakePHP 3.x
* PHP 5.4.16+

## Installation
You can require it with `composer require "davidyell/currency_exchange:3.x-dev"`

## Helper
The helper is provided to do the front-end conversion when displaying prices in different currencies. The helper takes a number of options to configure it's behaviour.

### Helper configuration
When you are adding the helper to your `$helpers` array in your controller, you can pass an array of options. The defaults 
are shown below as an example. If you're happy with the defaults you don't need to pass any configuration.

```php
// src/View/AppView.php
    $this->loadHelper('CurrencyExchange.Currency', ['targetCurrency' => 'GBP']);
```

## Shell
A shell is provided to allow updating of the cached exchange rate data via a cron job. This prevents the request having to wait to see if the remote api is available before returning data to the user.

Instead you can set the shell to run on a cron as often as you need and it will update the data in the background, keeping your front-end quick for your users and your data fresh.

## Updating the cache
The frequency at which the cache is updated is controller by how often you chose to run the shell task. I'd recommend setting a cron job which runs once a week to update the cache.

```bash
bin/cake currency_exchange.rates update GBP
```
