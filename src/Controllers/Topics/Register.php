<?php

declare(strict_types=1);

namespace App\Controllers\Topics;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

final class Register
{

    public function Register(string $topic, string $message)
    {
        global $ctrlMqtt;
        if ($topic == 'sdo/register')
        {
            // {"name": "gameserver0201", "ip": "10.0.0.1", "port": 7050}
            $jsonMessage = json_decode($message);

            // Because crash or just restart, search in DB if exists
            $myServer = \App\Models\Server::where('name', $jsonMessage->name)->first();
            if (!is_null($myServer))
            {
                $myServer->port = $jsonMessage->port;
                $myServer->current_players = 0;
                if ($myServer->id == 1)
                {
                    $myServer->is_free = false;
                }

                $myServer->save();
            } else {
                $serversCnt = \App\Models\Server::count();
                $server = new \App\Models\Server();

                $server->name = $jsonMessage->name;
                $server->port = $jsonMessage->port;
                $server->ip = $jsonMessage->ip;

                $server->x_start = -10000000;
                $server->x_end = 10000000;
                $server->y_start = -10000000;
                $server->y_end = 10000000;
                $server->z_start = -10000000;
                $server->z_end = 10000000;
                if ($serversCnt == 0)
                {
                $server->is_free = false;
                }
                $server->save();
            }

            // $serverdata = $server->registerGameServer($jsonMessage->name, $jsonMessage->port);
            // TODO
            \App\Controllers\Topics\Server::publishServersList();

            // publish on topic list of all players
            $topicPlayers = new \App\Controllers\Topics\Players();
            $topicPlayers->PublishPlayersList();
        } else {
            print_r($message);
            echo "\n";
        }
    }
}
