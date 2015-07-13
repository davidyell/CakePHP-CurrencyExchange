# Currency Exchange
A helper for CakePHP 2.x which will display a currency in a different currency using current exchange rates.

It will cache the result for a year and then check the response from the api before invalidating the cache to ensure that your site always serves some currency data, even if it's a little stale. This prevents your page having to wait for a response from the api whilst it's loading.

## Requirements
* CakePHP 2.4+

## Installation
There are two main installation methods depending on your setup. However, whichever one you chose you must include the bootstrap when loading the plugin, in order to load the cache configuration.

```php
CakePlugin::load('CurrencyExchange', ['bootstrap' => true]);
```

Make sure to include your CurrencyLayer API key in your `config/bootstrap.php` using `Configure::write('currencyLayer.apikey', "YourApiKeyGoesHere");`

### Manual plugin installation
Download the zip and unzip it to the `app/Plugin/CurrencyExchange` folder.

### Using Composer
You can require it with `composer require "davidyell/currency_exchange:dev-master"`

## Helper
The helper is provided to do the front-end conversion when displaying prices in different currencies. The helper takes a number of options to configure it's behaviour.

### Helper configuration
When you are adding the helper to your `$helpers` array in your controller, you can pass an array of options. The defaults are shown below as an example. If you're happy with the defaults you don't need to pass any configuration.

```php
// app/Controller/AppController.php
    public $helpers = [
        'CurrencyExchange.Currency'
    ];
    
// Examples/index.ctp
echo $this->Currency->display($price, 'GBP');
```

## Shell
A shell is provided to allow updating of the cached exchange rate data via a cron job. This prevents the request having to wait to see if the remote api is available before returning data to the user.

Instead you can set the shell to run on a cron as often as you need and it will update the data in the background, keeping your front-end quick for your users and your data fresh. Only if a valid response is recieved from the api will the cache be updated.

## Updating the cache
The frequency at which the cache is updated is controlled by how often you chose to run the shell task. I'd recommend setting a cron job which runs once a week to update the cache.
