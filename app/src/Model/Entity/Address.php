<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Address extends Entity
{
    protected $_accessible = [
        'store_id' => true,
        'postal_code' => true,
        'state' => true,
        'city' => true,
        'sublocality' => true,
        'street' => true,
        'street_number' => true,
        'complement' => true,
        'store' => true,
    ];
}
