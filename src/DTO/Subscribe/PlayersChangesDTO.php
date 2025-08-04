<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules;

readonly class PlayersChangesDTO
{

  /**
   * @param PlayersChangesAddDTO[] $add
   * @param PlayersChangesUpdateDTO[] $update
   * @param PlayersChangesDeleteDTO[] $delete
   */
  private function __construct(
    public array $add,
    public array $update,
    public array $delete,
    public int $server_id,
  )
  {
  }

  static public function make(array $data): PlayersChangesDTO
  {
    
    $to = v::create(
      new Rules\Key(
        'add', 
        new Rules\ArrayType(),
      ),
    )->isValid($data);

    v::create(
      new Rules\Key(
        'update', 
        new Rules\ArrayType(),
      ),
    )->isValid($data);

    v::create(
      new Rules\Key(
        'delete', 
        new Rules\ArrayType(),
      ),
    )->isValid($data);

    v::create(
      new Rules\Key(
        'server_id', 
        new Rules\IntType(),
      ),
    )->isValid($data);

    $dataAdd = [];
    foreach ($data['add'] as $add)
    {
      $dataAdd[] = PlayersChangesAddDTO::make($add);
    }

    $dataUpdate = [];
    foreach ($data['update'] as $update)
    {
      $dataUpdate[] = PlayersChangesUpdateDTO::make($update);
    }

    $dataDelete = [];
    foreach ($data['delete'] as $del)
    {
      $dataDelete[] = PlayersChangesDeleteDTO::make($del);
    }

    return new self($dataAdd, $dataUpdate, $dataDelete, $data['server_id']);
  }  
}
