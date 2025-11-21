<?php

namespace Tests\System;

use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class SeleniumCatalogTest extends TestCase
{
    public function test_catalogo_exhibe_el_formulario_de_busqueda(): void
    {
        if (!class_exists(RemoteWebDriver::class)) {
            $this->markTestSkipped('php-webdriver/webdriver no está disponible en este entorno.');
        }

        $serverUrl = getenv('SELENIUM_SERVER_URL') ?: 'http://localhost:4444/wd/hub';
        $appUrl = getenv('APP_URL') ?: 'http://localhost:8000';

        try {
            $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome(), 15000, 15000);
        } catch (WebDriverCurlException $e) {
            $this->markTestSkipped('No se pudo conectar con el servidor Selenium: ' . $e->getMessage());
            return;
        } catch (\Throwable $e) {
            $this->markTestSkipped('No se pudo inicializar Selenium: ' . $e->getMessage());
            return;
        }

        try {
            $driver->get($appUrl);

            $searchInput = $driver->findElement(WebDriverBy::cssSelector('input[name="q"]'));
            $this->assertStringContainsString('Buscar', $searchInput->getAttribute('placeholder'));

            $headerText = $driver->findElement(WebDriverBy::cssSelector('header'))->getText();
            $this->assertStringContainsString('Categoría', $headerText);
            $this->assertStringContainsString('Ordenar', $headerText);
        } finally {
            $driver->quit();
        }
    }
}
