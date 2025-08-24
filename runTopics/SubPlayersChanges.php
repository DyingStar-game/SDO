<?php

declare(strict_types=1);

use App\App;

require __DIR__ . '/../vendor/autoload.php';

$app = (new App())->get();

while (true) {
  try {
    $mqtt = new \App\Controllers\Mqtt();
    $mqtt->topicSubscribe('sdo/playerschanges', [\App\Controllers\Topics\Players::class, 'PlayersChanges']);
  } catch (Exception $e) {
    echo "crash: " . $e->getMessage();
  }
}
