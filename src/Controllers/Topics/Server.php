<?php

declare(strict_types=1);

namespace App\Controllers\Topics;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

final class Server
{
  public function ServerTooHeavy(string $topic, string $message, $mqttEnabled = true)
  {
    if ($topic == 'sdo/servertooheavy')
    {
      echo "received";
      // {"id": 45, "players": [{"x": "", "y": "", "z": ""}]}
      $jsonMessage = json_decode($message);
      $server = \App\Models\Server::where('id', (int) $jsonMessage->id)->first();

      $ctrlServer = new \App\Controllers\Server();
      try {
        $newServer = $ctrlServer->splitServer($server, $jsonMessage->players);
      } catch (Exception $e) {
        echo $e->getMessage();
        echo "No server available found :/";
        return;
      }
      $server->refresh();
      $server->to_split_server_id = $newServer->id;
      $server->save();

      // TODO we need return sdo/serverschanges 
      // a new server is activated, we send list of servers
      if ($mqttEnabled)
      {
        $this->publishServersChanges(add: [$newServer], update: [$server]);
        $this->publishServersList();
        // publish on topic list of all players
        $topicPlayers = new \App\Controllers\Topics\Players();
        $topicPlayers->PublishPlayersList();
      }
    }
  }

  public function ServerFree(string $topic, string $message)
  {
    if ($topic == 'sdo/serverfree')
    {
      // {"id": 6}
      $jsonMessage = json_decode($message);

      $server = \App\Models\Server::where('id', $jsonMessage->id)->first();
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
  
        if ($jsonMessage->id > 1)
        {
          // not free this server with id 1 because for the moment all players connect to this
          $server->is_free = true;
          $server->x_start = -10000000;
          $server->x_end = 10000000;
          $server->y_start = -10000000;
          $server->y_end = 10000000;
          $server->z_start = -10000000;
          $server->z_end = 10000000;
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
  
          if ($jsonMessage->id > 1)
          {
            // not free this server with id 1 because for the moment all players connect to this
            $server->is_free = true;
            $server->x_start = -10000000;
            $server->x_end = 10000000;
            $server->y_start = -10000000;
            $server->y_end = 10000000;
            $server->z_start = -10000000;
            $server->z_end = 10000000;
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
  
            if ($jsonMessage->id > 1)
            {
              // not free this server with id 1 because for the moment all players connect to this
              $server->is_free = true;
              $server->x_start = -10000000;
              $server->x_end = 10000000;
              $server->y_start = -10000000;
              $server->y_end = 10000000;
              $server->z_start = -10000000;
              $server->z_end = 10000000;
            }
          }
        }
        $server->save();
      }
      // a new server is free, we send list of servers
      $this->publishServersList();
    }
  }

  // publish on topic sdo/serverschanges
  public static function publishServersList()
  {
    global $ctrlMqtt;

    $serverslist = \App\Models\Server::
        where('is_free', false)
    ->select(['id', 'name', 'ip', 'port', 'x_start', 'x_end', 'y_start', 'y_end', 'z_start', 'z_end', 'to_split_server_id'])
    ->get();
    $ctrlMqtt->publish('sdo/serverslist', $serverslist->toJson());
  }

  public static function publishServersChanges($add = [], $update = [], $delete = [])
  {
    global $ctrlMqtt;

    $list = [
      "add" => [],
      "update" => [],
      "delete" => [],
    ];
    foreach ($add as $server)
    {
      $list["add"][] = [
        "id"                 => $server->id,
        "name"               => $server->name,
        "ip"                 => $server->ip,
        "port"               => $server->port,
        "x_start"            => $server->x_start,
        "x_end"              => $server->x_end,
        "y_start"            => $server->y_start,
        "y_end"              => $server->y_end,
        "z_start"            => $server->z_start,
        "z_end"              => $server->z_end,
        "to_merge_server_id" => $server->to_merge_server_id,
        "to_split_server_id" => $server->to_split_server_id,
      ];
    }
    foreach ($update as $server)
    {
      $list["update"][] = [
        "id"                 => $server->id,
        "x_start"            => $server->x_start,
        "x_end"              => $server->x_end,
        "y_start"            => $server->y_start,
        "y_end"              => $server->y_end,
        "z_start"            => $server->z_start,
        "z_end"              => $server->z_end,
        "to_merge_server_id" => $server->to_merge_server_id,
        "to_split_server_id" => $server->to_split_server_id,
      ];
    }    
    foreach ($delete as $server)
    {
      $list["delete"][] = [
        "id" => $server->id,
      ];
    }
    $ctrlMqtt->publish('sdo/serverschanges', json_encode($list));
  }
}
