<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PlayersCreation extends AbstractMigration
{
  public function change(): void
  {
    // create the table
    $table = $this->table('players');
    $table
      ->addColumn('name', 'string')
      ->addColumn('created_at', 'timestamp', ['null' => true])
      ->addColumn('updated_at', 'timestamp', ['null' => true])
      ->addColumn('deleted_at', 'timestamp', ['null' => true])
      ->addColumn('server_id', 'integer', ['null' => false])
      ->addColumn('client_uuid', 'string')
      ->addColumn('x', 'string')
      ->addColumn('y', 'string')
      ->addColumn('z', 'string')
      ->addColumn('xr', 'string')
      ->addColumn('yr', 'string')
      ->addColumn('zr', 'string')
      ->addIndex(['server_id'])
      ->addIndex(['client_uuid'])
      ->create();
  }
}
