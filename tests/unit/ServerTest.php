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
    $players = [
      ['x' => 0],
      ['x' => 10],
      ['x' => 60]
    ];

    $server = new \App\Controllers\Server();

    $reflection = new \ReflectionClass($server);
    $method = $reflection->getMethod('middlePlayersOnAxis');
    $method->setAccessible(true);

    $coordinate = $method->invoke($server, $players, 'x');
    $this->assertEquals(1000, $coordinate);
    print_r($$coordinate);


  }
}
