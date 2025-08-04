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
      ->addColumn('x_start', 'integer')
      ->addColumn('x_end', 'integer')
      ->addColumn('y_start', 'integer')
      ->addColumn('y_end', 'integer')
      ->addColumn('z_start', 'integer')
      ->addColumn('z_end', 'integer')
      ->addColumn('is_free', 'boolean', ['null' => false, 'default' => true])
      ->addColumn('is_online', 'boolean', ['null' => false, 'default' => true])
      ->addColumn('to_merge_server_id', 'boolean', ['null' => true])
      ->addColumn('x_size', 'integer')
      ->addColumn('y_size', 'integer')
      ->addColumn('z_size', 'integer')

      ->addIndex(['is_free'])
      ->addIndex(['is_free', 'x_size', 'y_size', 'z_size'])
      ->create();
  }
}
