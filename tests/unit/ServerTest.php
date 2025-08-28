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

  /**
   * @dataProvider provideDistanceMax
   */
  public function testDistanceMax($data, $expected): void
  {
    $message = '{"id":1,"players":[{"x":' . $data[0] . '},{"x":' . $data[1] . '},{"x":' . $data[2] . '}]}';

    $jsonMessage = json_decode($message);

    $server = new \App\Controllers\Server();

    $reflection = new \ReflectionClass($server);
    $method = $reflection->getMethod('distanceMax');
    $method->setAccessible(true);

    $distance = $method->invoke($server, $jsonMessage->players, 'x');

    $this->assertEquals($expected, $distance);
  }

  public static function provideDistanceMax()
  {
    return [
      [[-11.2676525115967, -15.7486991882324, 2.38979363441467], 18.13849282264707],
      [[1999.66943359375, 1999.66809082031, 1999.93762207031], 0.26953125],
      [[-24.9779491424561, -22.5385055541992, 4.65553188323975], 29.63348102569585],
    ];
  }
}
