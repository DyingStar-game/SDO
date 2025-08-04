<?php

declare(strict_types=1);

use App\App;

require __DIR__ . '/../vendor/autoload.php';

$app = (new App())->get();

$ctrlMqtt = new \App\Controllers\Mqtt();

// run all 30 seconds

$ctrlServer = new \App\Controllers\Server();
$ctrlServer->manageOneServerBranch([]);
