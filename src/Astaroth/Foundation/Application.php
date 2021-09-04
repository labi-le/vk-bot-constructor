<?php

declare(strict_types=1);

namespace Astaroth\Foundation;

use Astaroth\Auth\Configuration;
use Astaroth\Bootstrap\BotInstance;
use Astaroth\Handler\LazyHandler;
use Astaroth\Route\Route;
use Astaroth\Services\ServiceInterface;
use Astaroth\Support\Facades\FacadePlaceholder;
use HaydenPierce\ClassFinder\ClassFinder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Application
{
    public const VERSION = 2;

    public const DEV = "DEV";
    public const PRODUCTION = "PRODUCTION";

    private static ?ContainerBuilder $container = null;


    /**
     * Checks if the application is running in the console
     * @return bool
     */
    public static function runningInConsole(): bool
    {
        return \PHP_SAPI === "cli" || \PHP_SAPI === "phpdbg";
    }

    /**
     * @return ContainerBuilder
     */
    public static function getContainer(): ContainerBuilder
    {
        if (self::$container === null) {
            self::$container = new ContainerBuilder();
        }
        return self::$container;
    }

    /**
     * @throws \Throwable
     */
    public function run(string $dir = null, string $type = Application::DEV): void
    {
        $container = self::getContainer();
        $configuration = Configuration::set($dir, $type);

        array_walk($configuration, static fn($value, $key) => $container->setParameter($key, $value));

        foreach (ClassFinder::getClassesInNamespace(Configuration::SERVICE_NAMESPACE) as $service) {
            /** @var ServiceInterface $service */
            $service = new $service;
            $service($container);
        }

        FacadePlaceholder::getInstance($container);

        (new Route(
            new LazyHandler((new BotInstance($container))->bootstrap())))
            ->setClassMap((string)$container->getParameter(Configuration::APP_NAMESPACE))
            ->handle();
    }
}