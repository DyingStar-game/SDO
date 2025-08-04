<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

final class Player
{
  /**
   * @param array<string, string> $args
   */
  public function getAll(Request $request, Response $response, array $args): Response
  {
    $players = \App\Models\Player::get();
    $response->getBody()->write(json_encode($players));
    return $response->withHeader('Content-Type', 'application/json');
  }

}
