<?php

declare(strict_types=1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

final class ServerTest extends TestCase
{

  // protected $app;

  protected function setUp(): void
  {
    // $this->app = (new \App\App())->get();
  }

  // public function testMenuDataHasDisplayField(): void
  // {
  //   $user = \App\Models\User::find(1);
  // }

  public function testMiddlePlayersOnAxis(): void
  {
    $message = '{"id":1,"players":[{"x":0,"y":0,"z":2160},{"x":10,"y":0,"z":2158},{"x":60,"y":0,"z":2151}]}';
    $jsonMessage = json_decode($message);

    $server = new \App\Controllers\Server();

    $reflection = new \ReflectionClass($server);
    $method = $reflection->getMethod('middlePlayersOnAxis');
    $method->setAccessible(true);

    $coordinate = $method->invoke($server, $jsonMessage->players, 'x');
    $this->assertEquals(24, $coordinate);
  }

  public function testDistanceMax(): void
  {
    $message = '{"id":1,"players":[{"x":-5,"y":0,"z":2160},{"x":10,"y":0,"z":2158},{"x":60,"y":0,"z":2151}]}';
    $jsonMessage = json_decode($message);

    $server = new \App\Controllers\Server();

    $reflection = new \ReflectionClass($server);
    $method = $reflection->getMethod('distanceMax');
    $method->setAccessible(true);

    $distance = $method->invoke($server, $jsonMessage->players, 'x');

    $this->assertEquals(65, $distance);

  }
}
