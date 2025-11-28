<?php

namespace Tests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepara el driver de Chrome antes de la primera prueba.
     */
    public static function prepare(): void
    {
        static::startChromeDriver();
    }

    /**
     * Configura el navegador usado por Dusk.
     */
    protected function driver(): RemoteWebDriver
    {
        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()
        );
    }
}
