<?php

namespace App\Exceptions\Assignment;

use RuntimeException;

class DriverNotAvailableException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('El repartidor no está disponible para recibir este pedido.');
    }
}
