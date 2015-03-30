# Currency Exchange helper
A helper for CakePHP 2.x which will display a currency in a different currency using current exchange rates.

It will cache the result and check the response before invalidating the cache to ensure that your site always serves some currency data, even if it's a little stale.

## Requirements
* CakePHP 2.4+

## Installation
There are two main installation methods depending on your setup. However, whichever one you chose you must include the
bootstrap when loading the plugin, in order to load the cache configuration.

```php
CakePlugin::load('CurrencyExchange', ['bootstrap' => true]);
```

### Manual plugin installation
Download the zip and unzip it to the `app/Plugin/CurrencyExchange` folder.

### Using Composer
You can require it with `composer require "ukwm/currency_exchange:dev-master"`

## Helper
The helper is provided to do the front-end conversion when displaying prices in different currencies. The helper takes a 
number of options to configure it's behaviour.

### Helper configuration
When you are adding the helper to your `$helpers` array in your controller, you can pass an array of options. The defaults 
are shown below as an example. If you're happy with the defaults you don't need to pass any configuration.

```php
// app/Controller/AppController.php
    public $helpers = [
        'CurrencyExchange.Currency' => [
            'targetCurrency' => 'GBP', // The currency code to convert into
        ]
    ];
```

## Shell
A shell is provided to allow updating of the cached exchange rate data via a cron job. This prevent the request having to wait 
to see if the remote api is available before returning data to the user.

Instead you can set the shell to run on a cron as often as you need and it will update the data in the background, keeping your
front-end quick for your users and your data fresh.

## Updating the cache
The frequency at which the cache is updated is controller by how often you chose to run the shell task. I'd recommend 
setting a cron job which runs once a week to update the cache.