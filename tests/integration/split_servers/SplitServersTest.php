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

final class SplitServersTest extends TestCase
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
    \App\Models\Player::truncate();  

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
      $srv->is_free = $server->is_free;
      $srv->save();
    }

  }

  public function testServerRequestSplit(): void
  {
    // $this->reset_to_merge_server_id();

    $srv = \App\Models\Server::where('is_free', false)->first();

    $message = '{"id":' . $srv->id . ',"players":[{"x":2176,"y":0,"z":2160},{"x":2178,"y":0,"z":2158},{"x":2167,"y":0,"z":2151}]}';

    $topicServer = new \App\Controllers\Topics\Server();
    $topicServer->ServerTooHeavy(
      'sdo/servertooheavy',
      $message,
      false
    );

    $servers = \App\Models\Server::where('is_free', false)->get();
    $this->assertEquals(2, count($servers), 'must have 2 servers used');

  }


  // private function reset_to_merge_server_id()
  // {
  //   $servers = \App\Models\Server::get();
  //   foreach ($servers as $server)
  //   {
  //     $server->to_merge_server_id = null;
  //     $server->save();
  //   }
  // }
}
