<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules;

readonly class PlayersChangesUpdateDTO
{

  private function __construct(
    public string $client_uuid,
    public string|null $x,
    public string|null $y,
    public string|null $z,
    public string|null $xr,
    public string|null $yr,
    public string|null $zr,
  )
  {
  }

  static public function make(array $data): PlayersChangesUpdateDTO
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

    if (is_null($data['x']))
    {
      $data['x'] = null;
    } else {
      self::validateFloatValue($data, 'x');
    }

    if (is_null($data['y']))
    {
      $data['y'] = null;
    } else {
      self::validateFloatValue($data, 'y');
    }

    if (is_null($data['z']))
    {
      $data['z'] = null;
    } else {
      self::validateFloatValue($data, 'z');
    }

    if (is_null($data['xr']))
    {
      $data['xr'] = null;
    } else {
      self::validateFloatValue($data, 'xr');
    }

    if (is_null($data['yr']))
    {
      $data['yr'] = null;
    } else {
      self::validateFloatValue($data, 'yr');
    }

    if (is_null($data['zr']))
    {
      $data['zr'] = null;
    } else {
      self::validateFloatValue($data, 'zr');
    }

    return new self(
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
