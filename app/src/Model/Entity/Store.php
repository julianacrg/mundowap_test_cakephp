<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Store extends Entity
{
    protected $_accessible = [
        'name' => true,
        'addresses' => true,
    ];

    protected $_hidden = [
        'created',
        'modified',
    ];

}
