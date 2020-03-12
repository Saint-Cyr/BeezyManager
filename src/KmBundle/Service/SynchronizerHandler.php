<?php

namespace KmBundle\Service;

use KmBundle\Entity\Branch;
use TransactionBundle\Entity\Product;
use TransactionBundle\Entity\Stock;

class SynchronizerHandler
{
    //To store the entity manager
    private $client;
    
    public function __construct($client)
    {
        $this->client = $client;
    }

    public function start()
    {
        return true;
        //$this->client->pos('http://localhost/BeezyManager/web/app_dev.php/sales/transactions')
    }
}
