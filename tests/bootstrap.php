<?php

use Symfony\Component\ErrorHandler\ErrorHandler;

require_once 'vendor/autoload.php';

(new Symfony\Component\Dotenv\Dotenv())->loadEnv(__DIR__.'/../.env.test');

// https://github.com/symfony/symfony/issues/53812
set_exception_handler([new ErrorHandler(), 'handleException']);
