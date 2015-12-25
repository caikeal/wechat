<?php

use Overtrue\WeChat\Application;
use Overtrue\WeChat\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test __construct()
     */
    public function testConstructor()
    {
        $app = new Application(['foo' => 'bar']);

        $this->assertInstanceOf(Config::class, $app['config']);
        $providers = $app->getProviders();

        foreach ($providers as $provider) {
            $container = new Container();
            $container->register(new $provider());
            $container['config']       = $app->raw('config');
            $container['access_token'] = $app->raw('access_token');
            $container['request']      = $app->raw('request');
            $container['cache']        = $app->raw('cache');

            foreach ($container->keys() as $providerName) {
                $this->assertEquals($container->raw($providerName), $app->raw($providerName));
            }

            unset($container);
        }
    }

    /**
     * Test addProvider() and setProviders.
     */
    public function testProviders()
    {
        $app = new Application(['foo' => 'bar']);

        $providers = $app->getProviders();

        $app->addProvider(Mockery::mock(ServiceProviderInterface::class));

        $this->assertCount(count($providers) + 1, $app->getProviders());

        $app->setProviders(["foo", "bar"]);

        $this->assertEquals(["foo", "bar"], $app->getProviders());
    }
}