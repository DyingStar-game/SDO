<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ServerCreation extends AbstractMigration
{
  /**
   * Change Method.
   *
   * Write your reversible migrations using this method.
   *
   * More information on writing migrations is available here:
   * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
   *
   * Remember to call "create()" or "update()" and NOT "save()" when working
   * with the Table class.
   */
  public function change(): void
  {
    // create the table
    $table = $this->table('servers');
    $table
      ->addColumn('name', 'string')
      ->addColumn('created_at', 'timestamp', ['null' => true])
      ->addColumn('updated_at', 'timestamp', ['null' => true])
      ->addColumn('deleted_at', 'timestamp', ['null' => true])
      ->addColumn('version', 'string')
      ->addColumn('ip', 'string')
      ->addColumn('port', 'integer')
      ->addColumn('max_players', 'integer', ['null' => false, 'default' => 32])
      ->addColumn('current_players', 'integer', ['null' => false, 'default' => 0])
      ->addColumn('x_start', 'double')
      ->addColumn('x_end', 'double')
      ->addColumn('y_start', 'double')
      ->addColumn('y_end', 'double')
      ->addColumn('z_start', 'double')
      ->addColumn('z_end', 'double')
      ->addColumn('is_free', 'boolean', ['null' => false, 'default' => true])
      ->addColumn('is_online', 'boolean', ['null' => false, 'default' => true])
      ->addColumn('to_merge_server_id', 'boolean', ['null' => true])
      ->addColumn('to_split_server_id', 'boolean', ['null' => true])
      ->addColumn('x_size', 'double')
      ->addColumn('y_size', 'double')
      ->addColumn('z_size', 'double')

      ->addIndex(['is_free'])
      ->addIndex(['is_free', 'x_size', 'y_size', 'z_size'])
      ->create();
  }
}
