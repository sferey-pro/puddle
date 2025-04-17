<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;

require dirname(__DIR__).'/vendor/autoload.php';

/** @see https://github.com/symfony/symfony/issues/53812#issuecomment-1962740145 */
set_exception_handler([new ErrorHandler(), 'handleException']);

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" cache:clear --no-warmup',
    $_ENV['APP_ENV'],
    __DIR__
));

if (false === (bool) $_SERVER['APP_DEBUG']) {
    umask(0000);
    // ensure fresh cache
    (new Symfony\Component\Filesystem\Filesystem())->remove(dirname(__DIR__).'/var/cache/test');
}
