<?php

namespace Clippings\ParsedownProvider\Test;

use Clippings\ParsedownProvider\ParsedownServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * @coversDefaultClass \Clippings\ParsedownProvider\ParsedownServiceProvider
 */
class ParsedownServiceProviderTest extends TestCase
{
    /**
     * @covers ::register
     */
    public function testRegister()
    {
        $app = new Container();
        $app->register(new ParsedownServiceProvider());

        $this->assertArrayHasKey('parsedown.class', $app);
        $this->assertArrayHasKey('parsedown.breaks_enabled', $app);
        $this->assertArrayHasKey('parsedown.markup_escaped', $app);
        $this->assertArrayHasKey('parsedown.urls_linked', $app);
        $this->assertArrayHasKey('parsedown', $app);
        $this->assertArrayHasKey('parsedown.twig_filter', $app);

        $this->assertInstanceOf('Parsedown', $app['parsedown']);
        $this->assertSame($app['parsedown'], $app['parsedown']);

        $this->assertTrue($app['parsedown.breaks_enabled']);
        $this->assertFalse($app['parsedown.markup_escaped']);
        $this->assertTrue($app['parsedown.urls_linked']);

        $this->assertInstanceOf(
            'Twig_SimpleFilter',
            $app['parsedown.twig_filter']
        );
    }

    /**
     * @covers ::register
     */
    public function testLazines()
    {
        $app = new Container();
        $app->register(new ParsedownServiceProvider(), [
            'parsedown.urls_linked' => false,
        ]);
        $this->assertSame(
            '<p>https://example.com</p>',
            $app['parsedown']->text('https://example.com')
        );
    }

    /**
     * @covers ::register
     */
    public function testClassConfiguration()
    {
        $app = new Container();
        $app->register(new ParsedownServiceProvider(), [
            'parsedown.class' => 'ParsedownExtra',
        ]);
        $this->assertSame(
            '<h1 class="bar">Foo</h1>',
            $app['parsedown']->text('# Foo {.bar}')
        );
    }

    /**
     * @covers ::register
     */
    public function testTwigFilterIsRegistered()
    {
        $app = new Container();
        $app['twig'] = function () {
            return new Twig_Environment(new Twig_Loader_Array([]));
        };
        $app->register(new ParsedownServiceProvider());
        $this->assertSame($app['parsedown.twig_filter'], $app['twig']->getFilter('parsedown'));
    }
}
