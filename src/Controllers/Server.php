<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

final class Server
{
  private $server;

  /**
   * @param array<string, string> $args
   */
  public function postRegister(Request $request, Response $response, array $args): Response
  {
    $data = (array) $request->getParsedBody();

    if (!isset($data['name']) && !isset($data['port']))
    {
      throw new Exception("Data not right", 400);
    }

    $server = $this->registerGameServer();

    $response->getBody()->write(json_encode($server));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function registerGameServer($name, $port)
  {

    // Because crash or just restart, search in DB if exists
    $myServer = \App\Models\Server::where('name', $name)->first();
    if (!is_null($myServer))
    {
      $myServer->port = $port;
      $myServer->current_players = 0;

      $myServer->save();
      return $myServer;
    }

    $serversCnt = \App\Models\Server::count();
    $server = new \App\Models\Server();

    $server->name = $name;
    $server->port = $port;
    $server->ip = $_SERVER['REMOTE_ADDR'];

    $server->x_start = -10000000.0;
    $server->x_end = 10000000.0;
    $server->y_start = -10000000.0;
    $server->y_end = 10000000.0;
    $server->z_start = -10000000.0;
    $server->z_end = 10000000.0;
    if ($serversCnt == 0)
    {
      $server->is_free = false;
    }
    $server->save();
    return $server;
  }

  /**
   * @param array<string, string> $args
   */
  public function postTooHeavy(Request $request, Response $response, array $args): Response
  {
    $serverId = $args['id'];
    $data = (array) $request->getParsedBody();


    $server = \App\Models\Server::where('id', $serverId)->first();
    $newServer = $this->splitServer($server, json_decode($data['players'], true));

    $response->getBody()->write(json_encode($newServer));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<string, string> $args
   */
  public function postFree(Request $request, Response $response, array $args): Response
  {
    $serverId = $args['id'];

    $server = \App\Models\Server::where('id', $serverId)->first();
    if (is_null($server))
    {
      throw new Exception("Server not found", 404);
    }

    // Find another server have 2 axis same
    $serverMerge = \App\Models\Server::
        where('x_start', $server->x_start)
      ->where('x_end', $server->x_end)
      ->where('y_start', $server->y_start)
      ->where('y_end', $server->y_end)
      ->where('is_free', false)
      ->first();
    if (!is_null($serverMerge))
    {
      // we merge
      if ($serverMerge->z_start < $server->z_start)
      {
        $serverMerge->z_end = $server->z_end;
      } else {
        $serverMerge->z_start = $server->z_start;
      }
      $serverMerge->save();

      if ($serverId > 1)
      {
        // not free this server with id 1 because for the moment all players connect to this
        $server->is_free = true;
        $server->x_start = -10000000.0;
        $server->x_end = 10000000.0;
        $server->y_start = -10000000.0;
        $server->y_end = 10000000.0;
        $server->z_start = -10000000.0;
        $server->z_end = 10000000.0;
      }

    } else {
      $serverMerge = \App\Models\Server::
          where('x_start', $server->x_start)
        ->where('x_end', $server->x_end)
        ->where('z_start', $server->z_start)
        ->where('z_end', $server->z_end)
        ->where('is_free', false)
        ->first();
      if (!is_null($serverMerge))
      {
        // we merge
        if ($serverMerge->y_start < $server->y_start)
        {
          $serverMerge->y_end = $server->y_end;
        } else {
          $serverMerge->y_start = $server->y_start;
        }
        $serverMerge->save();

        if ($serverId > 1)
        {
          // not free this server with id 1 because for the moment all players connect to this
          $server->is_free = true;
          $server->x_start = -10000000.0;
          $server->x_end = 10000000.0;
          $server->y_start = -10000000.0;
          $server->y_end = 10000000.0;
          $server->z_start = -10000000.0;
          $server->z_end = 10000000.0;
        }
      } else {
        $serverMerge = \App\Models\Server::
            where('y_start', $server->y_start)
          ->where('y_end', $server->y_end)
          ->where('z_start', $server->z_start)
          ->where('z_end', $server->z_end)
          ->where('is_free', false)
          ->first();
        if (!is_null($serverMerge))
        {
          // we merge
          if ($serverMerge->x_start < $server->x_start)
          {
            $serverMerge->x_end = $server->x_end;
          } else {
            $serverMerge->x_start = $server->x_start;
          }
          $serverMerge->save();

          if ($serverId > 1)
          {
            // not free this server with id 1 because for the moment all players connect to this
            $server->is_free = true;
            $server->x_start = -10000000.0;
            $server->x_end = 10000000.0;
            $server->y_start = -10000000.0;
            $server->y_end = 10000000.0;
            $server->z_start = -10000000.0;
            $server->z_end = 10000000.0;
          }
        }
      }
      $server->save();

      $response->getBody()->write(json_encode([]));
      return $response->withHeader('Content-Type', 'application/json');
    }

    $serverMerge = \App\Models\Server::
        where('x_start', $server->x_start)
      ->where('x_end', $server->x_end)
      ->where('z_start', $server->z_start)
      ->where('z_end', $server->z_end)
      ->where('is_free', false)
      ->first();
    if (!is_null($serverMerge))
    {
      // we merge
      if ($serverMerge->y_start < $server->y_start)
      {
        $serverMerge->y_end = $server->y_end;
      } else {
        $serverMerge->y_start = $server->y_start;
      }
      $serverMerge->save();

      $server->is_free = true;
      $server->save();

      $response->getBody()->write(json_encode([]));
      return $response->withHeader('Content-Type', 'application/json');
    }

    $serverMerge = \App\Models\Server::
        where('y_start', $server->y_start)
      ->where('y_end', $server->y_end)
      ->where('z_start', $server->z_start)
      ->where('z_end', $server->z_end)
      ->where('is_free', false)
      ->first();
    if (!is_null($serverMerge))
    {
      // we merge
      if ($serverMerge->x_start < $server->x_start)
      {
        $serverMerge->x_end = $server->x_end;
      } else {
        $serverMerge->x_start = $server->x_start;
      }
      $serverMerge->save();

      $server->is_free = true;
      $server->save();

      $response->getBody()->write(json_encode([]));
      return $response->withHeader('Content-Type', 'application/json');
    }
    throw new Exception("No neighbor server found", 500);
  }

  /**
   * @param array<string, string> $args
   */
  public function postPlayers(Request $request, Response $response, array $args): Response
  {
    $serverId = $args['id'];
    $data = (array) $request->getParsedBody();

    $server = \App\Models\Server::where('id', $serverId)->first();
    if (is_null($server))
    {
      throw new Exception('The server not exists', 404);
      
    }

    $playersGameServer = [];
    foreach (json_decode($data['players']) as $playerGameServer)
    {
      $playersGameServer[$playerGameServer->client_uuid] = $playerGameServer;
    }

    $players = \App\Models\Player::where('server_id', $serverId)->get();
    $dbPlayers = [];
    foreach ($players as $player)
    {
      if (isset($playersGameServer[$player->client_uuid]))
      {
        // Update
        $player->x = $playersGameServer[$player->client_uuid]->x;
        $player->y = $playersGameServer[$player->client_uuid]->y;
        $player->z = $playersGameServer[$player->client_uuid]->z;
        $player->save();
        $dbPlayers[$player->client_uuid] = true;
      } else {
        // delete
        $player->delete();
      }
    }

    foreach ($playersGameServer as $playerGameServer)
    {
      if (!isset($dbPlayers[$playerGameServer->client_uuid]))
      {
        // create
        $player = new \App\Models\Player();
        $player->name = $playerGameServer->name;
        $player->server_id = $serverId;
        $player->client_uuid = $playerGameServer->client_uuid;
        $player->x = $playerGameServer->x;
        $player->y = $playerGameServer->y;
        $player->z = $playerGameServer->z;
        $player->save();
      }
    }
    // update current players in server
    $server->current_players = count($data['players']);
    $server->save();

    $response->getBody()->write(json_encode([]));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<string, string> $args
   */
  public function getAll(Request $request, Response $response, array $args): Response
  {
    $server = \App\Models\Server::get();
    $response->getBody()->write(json_encode($server));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<string, string> $args
   */
  public function getAllActiveOnly(Request $request, Response $response, array $args): Response
  {
    $server = \App\Models\Server::where('is_free', false)->get();
    $response->getBody()->write(json_encode($server));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<string, string> $args
   */
  public function getItem(Request $request, Response $response, array $args): Response
  {
    $serverId = $args['id'];

    $server = \App\Models\Server::where('id', $serverId)->first();
    if (is_null($server))
    {
      throw new Exception("Server not found", 404);
    }

    $response->getBody()->write(json_encode($server));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Because a server is too heavy, we split it
   */
  public function splitServer(\App\Models\Server $server, $players)
  {
    // detect the x, y of z where the players have the greater distance.
    $values = [
      'x' => $this->distanceMax($players, 'x'),
      'y' => $this->distanceMax($players, 'y'),
      'z' => $this->distanceMax($players, 'z'),
    ];
    $fixedAxis = '';
    arsort($values);
    reset($values);
    $fixedAxis = key($values);

    // Get the middle of the greater distance for cutting the server zone
    switch ($fixedAxis) {
      case 'x':
        $middle = $this->middlePlayersOnAxis($players, 'x');
        $newServer = $this->createNewServer(
          $middle,
          $server->x_end,
          $server->y_start,
          $server->y_end,
          $server->z_start,
          $server->z_end,
        );
        $this->setServerNewZone($server, $middle, 'x');
        return $newServer;
        break;
      
      case 'y':
        $middle = $this->middlePlayersOnAxis($players, 'y');
        $newServer = $this->createNewServer(
          $server->x_start,
          $server->x_end,
          $middle,
          $server->y_end,
          $server->z_start,
          $server->z_end,
        );
        $this->setServerNewZone($server, $middle, 'y');
        return $newServer;
        break;
      
      case 'z':
        $middle = $this->middlePlayersOnAxis($players, 'z');
        $newServer = $this->createNewServer(
          $server->x_start,
          $server->x_end,
          $server->y_start,
          $server->y_end,
          $middle,
          $server->z_end,
        );
        $this->setServerNewZone($server, $middle, 'z');
        return $newServer;
        break;
    }
    throw new Exception("Error", 500);
  }

  /**
   * Get the max distance
   */
  private function distanceMax(array $players, string $property)
  {
    $min = round($players[0]->{$property}, 20);
    $max = round($players[0]->{$property}, 20);
    foreach ($players as $player)
    {
      if (round($player->{$property}, 20) < $min)
      {
        $min = round($player->{$property}, 20);
      }
      if (round($player->{$property}, 20) > $max)
      {
        $max = round($player->{$property}, 20);
      }
    }
    return $max - $min;
  }

  // TODO perhaps get the median
  private function middlePlayersOnAxis($players, $property)
  {
    $sum = 0;
    foreach ($players as $player)
    {
      $sum += $player->{$property};
    }
    return round($sum / count($players), 10);

    // median TODO

    $values = [];
    foreach ($players as $player)
    {
      $values[] = $player->{$property};
    }

    $count = count($values); //total numbers in array
    $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $values[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $values[$middleval];
        $high = $values[$middleval + 1];
        $median = (($low + $high) / 2);
    }
    return $median;

  }

  private function setServerNewZone(\App\Models\Server $server, $endValue, $axis): void
  {
    switch ($axis) {
      case 'x':
        $server->x_end = $endValue;
        break;
      
      case 'y':
        $server->y_end = $endValue;
        break;

      case 'z':
        $server->z_end = $endValue;
        break;
  
    }
    $server->save();
  }

  /**
   * Create a new game server, use free server and set the zone
   */
  private function createNewServer(
    float $xStart,
    float $xEnd,
    float $yStart,
    float $yEnd,
    float $zStart,
    float $zEnd,
  )
  {
    $freeServer = \App\Models\Server::where('is_free', true)->first();
    if (is_null($freeServer))
    {
      throw new Exception("No free server available", 500);
    }
    $freeServer->x_start = $xStart;
    $freeServer->x_end = $xEnd;
    $freeServer->y_start = $yStart;
    $freeServer->y_end = $yEnd;
    $freeServer->z_start = $zStart;
    $freeServer->z_end = $zEnd;
    $freeServer->is_free = false;
    $freeServer->save();
    return $freeServer;
  }
















  /**
   * 
   * @param array<int> list of servers yet checked
   */
  public function manageOneServerBranch(array $serverIds, $publish = true)
  {
    global $serversModifications;

    $serversModifications = [];
    $serversToPublish = [];

    // Minimum of players to try close server and push to another.
    $minPlayers = 6;
    // $minPlayers = 0;
    // Max players a server could have (with previous server) to be merged
    $maxPlayers = 22;
    // $maxPlayers = 32;

    $servers = \App\Models\Server::
        where('is_free', false)
      ->whereNotIn('id', $serverIds)
      ->orderBy('x_size', 'asc')
      ->orderBy('y_size', 'asc')
      ->orderBy('z_size', 'asc')
      ->get();

    foreach ($servers as &$server)
    {
      if (
          ($server->current_players > $minPlayers) ||
          (
            isset($serversModifications[$server->id]) &&
            !is_null($serversModifications[$server->id])
          )
      )
      {
        // No merge, next
        continue;
      }
      $this->server = $server;
      // get parent servers have 2 coordinates same
      $parentServers = array_merge(
        array_filter($servers->all(), [$this, 'getParentServersXY']),
        array_filter($servers->all(), [$this, 'getParentServersXZ']),
        array_filter($servers->all(), [$this, 'getParentServersYZ'])
      );
      // fetch parents
      foreach ($parentServers as $pserver)
      {
        if (
            $pserver->current_players <= $maxPlayers &&
            !isset($serversModifications[$server->id])
        )
        {
          echo $server->id ." - " . $pserver->id . "\n";
          // Yeah, we can merge both
          $serversModifications[$server->id] = [
            'current_players'    => $server->current_players,
            'to_merge_server_id' => $pserver->id,
          ];
          // Update in server the new zone
          $c = $this->mergeTwoServersCoordinates($server, $pserver);
          $server->x_start = $c['x_start'];
          $server->x_end   = $c['x_end'];
          $server->y_start = $c['y_start'];
          $server->y_end   = $c['y_end'];
          $server->z_start = $c['z_start'];
          $server->z_end   = $c['z_end'];

          $serversToPublish[$server->id] = $server;
          $serversToPublish[$pserver->id] = $pserver;


          // $serversModifications[$pserver->id] = [
          //   'current_players'    => $pserver->current_players,
          //   'to_merge_server_id' => null,
          // ];
          break;
        }
      }
    }

    // Update in DB
    foreach ($servers as $server)
    {
      foreach (array_keys($serversModifications) as $keyId)
      {
        if ($server->id == $keyId)
        {
          $server->to_merge_server_id = $serversModifications[$keyId]['to_merge_server_id'];
          $server->save();
          break;
        }
      }
      $server->save();
    }

    // Publish new server list
    if ($publish)
    {
      \App\Controllers\Topics\Server::publishServersList();
      \App\Controllers\Topics\Server::publishServersChanges([], $serversToPublish);
    }
    return $serversToPublish;
    // return $serversModifications;
  }

  /**
   * Filter (array_map) to have servers have same x and y
   */
  private function getParentServersXY($a)
  {
    return (
      $this->server->x_start == $a->x_start &&
      $this->server->x_end   == $a->x_end &&
      $this->server->y_start == $a->y_start &&
      $this->server->y_end   == $a->y_end &&
      $this->server->id != $a->id
    );
  }

  /**
   * Filter (array_map) to have servers have same x and z
   */
  private function getParentServersXZ($a)
  {
    return (
      $this->server->x_start == $a->x_start &&
      $this->server->x_end   == $a->x_end &&
      $this->server->z_start == $a->z_start &&
      $this->server->z_end   == $a->z_end &&
      $this->server->id != $a->id
    );
  }

  /**
   * Filter (array_map) to have servers have same y and z
   */
  private function getParentServersYZ($a)
  {
    return (
      $this->server-> y_start == $a->y_start &&
      $this->server->y_end   == $a->y_end &&
      $this->server->z_start == $a->z_start &&
      $this->server->z_end   == $a->z_end &&
      $this->server->id != $a->id
    );
  }

  private function mergeTwoServersCoordinates($server, $pserver)
  {
    $c = [
      "x_start" => 0.0,
      "x_end"   => 0.0,
      "y_start" => 0.0,
      "y_end"   => 0.0,
      "z_start" => 0.0,
      "z_end"   => 0.0,
    ];
    if ($server->x_start < $pserver->x_start)
    {
      $c['x_start'] = $server->x_start;
    } else {
      $c['x_start'] = $pserver->x_start;
    }
    if ($server->x_end > $pserver->x_end)
    {
      $c['x_end'] = $server->x_end;
    } else {
      $c['x_end'] = $pserver->x_end;
    }

    if ($server->y_start < $pserver->y_start)
    {
      $c['y_start'] = $server->y_start;
    } else {
      $c['y_start'] = $pserver->y_start;
    }
    if ($server->y_end > $pserver->y_end)
    {
      $c['y_end'] = $server->y_end;
    } else {
      $c['y_end'] = $pserver->y_end;
    }

    if ($server->z_start < $pserver->z_start)
    {
      $c['z_start'] = $server->z_start;
    } else {
      $c['z_start'] = $pserver->z_start;
    }
    if ($server->z_end > $pserver->z_end)
    {
      $c['z_end'] = $server->z_end;
    } else {
      $c['z_end'] = $pserver->z_end;
    }
    return $c;
  }
}
