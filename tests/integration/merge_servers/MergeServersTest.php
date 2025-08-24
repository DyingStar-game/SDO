<?php

declare(strict_types=1);

namespace Tests\integration\merge_servers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Slim\Factory\AppFactory;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

final class MergeServersTest extends TestCase
{

  protected $app;

  protected function setUp(): void
  {
    $app = AppFactory::create();
    $app->setBasePath('/sdo');
    $dbConfig = include(__DIR__ . '/../../../phinx.php');
    
    $capsule = new Capsule();
    $myDatabase = $dbConfig['environments'][$dbConfig['environments']['default_environment']];
    $configdb = [
      'driver'    => $myDatabase['adapter'],
      'host'      => $myDatabase['host'],
      'database'  => $myDatabase['name'],
      'username'  => $myDatabase['user'],
      'password'  => $myDatabase['pass'],
      'charset'   => $myDatabase['charset'],
      // 'collation' => $myDatabase['collation'],
    ];
    $capsule->addConnection($configdb);
    $capsule->setEventDispatcher(new Dispatcher(new Container()));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();


    \App\Models\Server::truncate();  

    // load data set
    $servers = json_decode(file_get_contents(__DIR__ . '/data/servers.json'));
    foreach ($servers as $server)
    {
      $srv = new \App\Models\Server();
      $srv->name = $server->name;
      $srv->max_players = $server->max_players;
      $srv->current_players = $server->current_players;
      $srv->x_start = $server->x_start;
      $srv->x_end = $server->x_end;
      $srv->y_start = $server->y_start;
      $srv->y_end = $server->y_end;
      $srv->z_start = $server->z_start;
      $srv->z_end = $server->z_end;
      $srv->x_size = $server->x_size;
      $srv->y_size = $server->y_size;
      $srv->z_size = $server->z_size;
      $srv->is_free = false;
      $srv->save();
    }

  }

  public function testMergeSomeServersOnParent(): void
  {
    $this->reset_to_merge_server_id();

    // Set players count
    $defaultPlayersNumber = 30;
    $specificPlayersNumber = [
      'gameserver0102' => 4,
      'gameserver0107' => 12,
      'gameserver0108' => 2,
    ];
    $this->setCurrentPlayerNumber($defaultPlayersNumber, $specificPlayersNumber);

// little
// gameserver0102 => no merge
// gameserver0108 => merge with 107

    // run CRON
    $ctrlServer = new \App\Controllers\Server();
    $ctrlServer->manageOneServerBranch([]);

    $serversToMerge = \App\Models\Server::
        whereNotNull('to_merge_server_id')
      ->get();

    $this->assertEquals(1, count($serversToMerge));

    $serverDestination = \App\Models\Server::where('name', 'gameserver0107')->first();
    $this->assertNotNull($serverDestination);

    $this->assertEquals($serverDestination->id, $serversToMerge[0]['to_merge_server_id']);
  }

  public function testMergeMultipleServersSameTime(): void
  {
    $this->reset_to_merge_server_id();

    // Set players count
    $defaultPlayersNumber = 30;
    $specificPlayersNumber = [
      'gameserver0102' => 4,
      'gameserver0106' => 8,
      'gameserver0107' => 12, // must merge on 106
      'gameserver0108' => 2, // must merge on 107, then 106
    ];
    $this->setCurrentPlayerNumber($defaultPlayersNumber, $specificPlayersNumber);

    // run CRON
    $ctrlServer = new \App\Controllers\Server();
    $ctrlServer->manageOneServerBranch([]);

    $serversToMerge = \App\Models\Server::
        whereNotNull('to_merge_server_id')
      ->get();

    $this->assertEquals(2, count($serversToMerge));

  }

  private function setCurrentPlayerNumber(int $default, array $specific)
  {
    $servers = \App\Models\Server::get();
    foreach ($servers as $server)
    {
      if (isset($specific[$server->name]))
      {
        $server->current_players = $specific[$server->name];
      } else {
        $server->current_players = $default;
      }
      $server->save();
    }
  }

  private function reset_to_merge_server_id()
  {
    $servers = \App\Models\Server::get();
    foreach ($servers as $server)
    {
      $server->to_merge_server_id = null;
      $server->save();
    }
  }
}
