<?php

use Symfony\Component\Dotenv\Dotenv;
use App\Kernel;

require __DIR__ . '/../vendor/autoload.php';

$envPath = __DIR__ . '.env';

if (file_exists($envPath)) {
    // Load in System Environments Variables for config parameters, Dont do this for test where these are
    // set via parameters_test.yml
    $dotenv = new Dotenv(false);
    $dotenv->load($envPath);
}

$kernel = new Kernel('dev', true);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
