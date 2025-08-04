<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules;

readonly class PlayersChangesAddDTO
{

  private function __construct(
    public string $name,
    public string $client_uuid,
    public string $x,
    public string $y,
    public string $z,
    public string $xr,
    public string $yr,
    public string $zr,
  )
  {
  }

  static public function make(array $data): PlayersChangesAddDTO
  {
    v::create(
      new Rules\Key(
        'name',
        new Rules\AllOf(
          new Rules\StringType(),
          new Rules\NotEmpty(),
        ) 
      ),
    )->isValid($data);

    v::create(
      new Rules\Key(
        'client_uuid',
        new Rules\AllOf(
          new Rules\StringType(),
          new Rules\Uuid(4),
        )
      ),
    )->isValid($data);

    self::validateFloatValue($data, 'x');
    self::validateFloatValue($data, 'y');
    self::validateFloatValue($data, 'z');

    self::validateFloatValue($data, 'xr');
    self::validateFloatValue($data, 'yr');
    self::validateFloatValue($data, 'zr');

    return new self(
      $data['name'],
      $data['client_uuid'],
      $data['x'],
      $data['y'],
      $data['z'],
      $data['xr'],
      $data['yr'],
      $data['zr'],
    );
  }

  /**
   * 
   * @param array<string, float> $data
   */
  private static function validateFloatValue(array $data, string $key)
  {
    v::create(
      new Rules\Key(
        $key,
        new Rules\AllOf(
          new Rules\StringType(),
          new Rules\FloatVal(),
        )
      ),
    )->isValid($data);
  }
}
