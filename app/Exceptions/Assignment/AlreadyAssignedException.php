<?php

namespace App\Exceptions\Assignment;

use RuntimeException;

class AlreadyAssignedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Este pedido ya tiene un repartidor asignado activamente.');
    }
}
