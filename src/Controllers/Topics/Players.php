<?php

declare(strict_types=1);

namespace App\Controllers\Topics;

use App\DTO\Request\PlayersChangesDTO;


final class Players
{
  public function PlayersChanges(string $topic, string $message)
  {
    if ($topic == 'sdo/playerschanges')
    {
      // {"add": [], "update": [], "delete": [], "server_id": 5}
      // $jsonMessage = PlayersChangesDTO::make(json_decode($message, true));
      $jsonMessage = json_decode($message);
      foreach ($jsonMessage->add as $dataPlayer)
      {
        // check if player exists (in case player has changed server)
        $player = \App\Models\Player::
            where("client_uuid", $dataPlayer->client_uuid)
          ->first();
        if (is_null($player))
        {
          // create
          $p = new \App\Models\Player();
          $p->name = $dataPlayer->name;
          $p->server_id = $jsonMessage->server_id;
          $p->client_uuid = $dataPlayer->client_uuid;
          if (!is_null($dataPlayer->x))
          {
            $p->x = $dataPlayer->x;
          }
          if (!is_null($dataPlayer->y))
          {
            $p->y = $dataPlayer->y;
          }
          if (!is_null($dataPlayer->z))
          {
            $p->z = $dataPlayer->z;
          }

          if (!is_null($dataPlayer->xr))
          {
            $p->xr = $dataPlayer->xr;
          }
          if (!is_null($dataPlayer->yr))
          {
            $p->yr = $dataPlayer->yr;
          }
          if (!is_null($dataPlayer->zr))
          {
            $p->zr = $dataPlayer->zr;
          }
          $p->save();
        } else {
          // update
          $player->name = $dataPlayer->name;
          $player->server_id = $jsonMessage->server_id;
          if (!is_null($dataPlayer->x))
          {
            $player->x = $dataPlayer->x;
          }
          if (!is_null($dataPlayer->y))
          {
            $player->y = $dataPlayer->y;
          }
          if (!is_null($dataPlayer->z))
          {
            $player->z = $dataPlayer->z;
          }

          if (!is_null($dataPlayer->xr))
          {
            $player->xr = $dataPlayer->xr;
          }
          if (!is_null($dataPlayer->yr))
          {
            $player->yr = $dataPlayer->yr;
          }
          if (!is_null($dataPlayer->zr))
          {
            $player->zr = $dataPlayer->zr;
          }
          $player->save();
        }
      }
      foreach ($jsonMessage->update as $dataPlayer)
      {
        $player = \App\Models\Player::
            where("client_uuid", $dataPlayer->client_uuid)
          ->first();
        if (is_null($player))
        {
          echo "Problem with update player not in DB!!!\n";
          print_r($dataPlayer);
        } else {
          $player->server_id = $jsonMessage->server_id;
          if (!is_null($dataPlayer->x))
          {
            $player->x = $dataPlayer->x;
          }
          if (!is_null($dataPlayer->y))
          {
            $player->y = $dataPlayer->y;
          }
          if (!is_null($dataPlayer->z))
          {
            $player->z = $dataPlayer->z;
          }

          if (!is_null($dataPlayer->xr))
          {
            $player->xr = $dataPlayer->xr;
          }
          if (!is_null($dataPlayer->yr))
          {
            $player->yr = $dataPlayer->yr;
          }
          if (!is_null($dataPlayer->zr))
          {
            $player->zr = $dataPlayer->zr;
          }
          $player->save();
        }
      }
      foreach ($jsonMessage->delete as $dataPlayer)
      {
        // {
        //   "client_uuid": ""
        // }
        $player = \App\Models\Player::
            where("client_uuid", $dataPlayer->client_uuid)
          ->first();
        if (is_null($player))
        {
          echo "Problem with delete player, not in DB!!!\n";
          print_r($dataPlayer);
        } else {
          $player->delete();
        }
      }
    }
    // finish, no actions after that.
  }

  public function PublishPlayersList()
  {
    global $ctrlMqtt;

    $players = \App\Models\Player::
        select(['name', 'client_uuid', 'server_id', 'x', 'y', 'z', 'xr', 'yr', 'zr'])
      ->get();
    $ctrlMqtt->publish('sdo/playerslist', $players->toJson());
  }
}
