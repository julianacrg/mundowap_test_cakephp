<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AddressesTable extends Table
{

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('addresses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Stores', [
            'foreignKey' => 'store_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('store_id')
            ->notEmptyString('store_id');

        $validator
            ->scalar('postal_code')
            ->maxLength('postal_code', 8)
            ->requirePresence('postal_code', 'create')
            ->notEmptyString('postal_code');

        $validator
            ->scalar('state')
            ->maxLength('state', 2)
            ->requirePresence('state', 'create')
            ->notEmptyString('state');

        $validator
            ->scalar('city')
            ->maxLength('city', 200)
            ->requirePresence('city', 'create')
            ->notEmptyString('city');

        $validator
            ->scalar('sublocality')
            ->maxLength('sublocality', 200)
            ->requirePresence('sublocality', 'create')
            ->notEmptyString('sublocality');

        $validator
            ->scalar('street')
            ->maxLength('street', 200)
            ->requirePresence('street', 'create')
            ->notEmptyString('street');

        $validator
            ->scalar('street_number')
            ->maxLength('street_number', 200)
            ->requirePresence('street_number', 'create')
            ->notEmptyString('street_number');

        $validator
            ->scalar('complement')
            ->maxLength('complement', 200);

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('store_id', 'Stores'), ['errorField' => 'store_id']);

        return $rules;
    }
}
