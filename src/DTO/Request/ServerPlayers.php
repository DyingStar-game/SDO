<?php

declare(strict_types=1);

namespace App\DTO\Request;

readonly class ServerPlayers
{

  function __construct(
    private int $x,
    private int $y,
    private int $z,
  )
  {
  }

  
}
