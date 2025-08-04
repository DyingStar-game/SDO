<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules;

readonly class PlayersChangesDeleteDTO
{

  private function __construct(
    public string $client_uuid,
  )
  {
  }

  static public function make(array $data): PlayersChangesDeleteDTO
  {
    v::create(
      new Rules\Key(
        'client_uuid',
        new Rules\AllOf(
          new Rules\StringType(),
          new Rules\Uuid(4),
        )
      ),
    )->isValid($data);

    return new self(
      $data['client_uuid'],
    );
  }
}
