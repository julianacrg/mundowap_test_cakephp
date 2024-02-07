<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Addresses extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('addresses');
        $table
            ->addColumn('store_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('postal_code', 'string', [
                'limit' => 8,
                'null' => false,
            ])
            ->addColumn('state', 'string', [
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('city', 'string', [
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('sublocality', 'string', [
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('street', 'string', [
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('street_number', 'string', [
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('complement', 'string', [
                'default' => '',
                'limit' => 200,
                'null' => false,
            ])
            ->addForeignKey('store_id', 'stores', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addIndex(['store_id'], ['unique' => true])
            ->create();
    }
}
