<?php

declare(strict_types=1);

namespace App\Controllers;

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

final class Mqtt
{
    private MqttClient $MqttClient; 

    function __construct()
    {
        // $server   = 'mqtt.dev.stardeception.space';
        // $port     = 1884;
        $server   = '127.0.0.1';
        $port     = 1883;
        $clientId = rand(10, 20);
        // $username = 'emqx_user';
        // $password = 'public';
        $clean_session = false;
        $mqtt_version = MqttClient::MQTT_3_1_1;

        $connectionSettings = (new ConnectionSettings)
        //   ->setUsername($username)
        //   ->setPassword($password)
          ->setKeepAliveInterval(60)
          // ->setLastWillTopic('emqx/test/last-will')
          ->setLastWillMessage('client disconnect')
          ->setLastWillQualityOfService(1);
          
          
          $this->MqttClient = new MqttClient($server, $port, strval($clientId), $mqtt_version);
          $this->MqttClient->connect($connectionSettings, $clean_session);
    }

    public function topicSubscribe($topic, array $callback)
    {
        $this->MqttClient->subscribe($topic, function (string $topic, string $message) use ($callback) {
          $class = new $callback[0]();
          call_user_func_array([$class, $callback[1]], [$topic, $message]);
        }, 0);
        $this->MqttClient->loop(true);
    }

    public function publish($topic, $message)
    {
      // echo "We publish... \n";
      // print_r($message);
      // BE CAREFULL, if message empty, not send it

      $this->MqttClient->publish($topic, $message, 0);
    }

}
