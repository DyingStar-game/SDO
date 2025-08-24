<?php

declare(strict_types=1);

use App\App;

require __DIR__ . '/../vendor/autoload.php';

$app = (new App())->get();

while (true) {
  try {
    $ctrlMqtt = new \App\Controllers\Mqtt();
    $ctrlMqtt->topicSubscribe('sdo/servertooheavy', [\App\Controllers\Topics\Server::class, 'ServerTooHeavy']);
  } catch (Exception $e) {
    echo "crash: " . $e->getMessage();
  }
}
