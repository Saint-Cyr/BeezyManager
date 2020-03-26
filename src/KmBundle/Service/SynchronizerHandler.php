<?php

namespace KmBundle\Service;

use KmBundle\Entity\Branch;
use TransactionBundle\Entity\Product;
use TransactionBundle\Entity\Stock;

/*
 * The methodes of this class are tested from the BSol Client
 */
class SynchronizerHandler
{
    //To store the entity manager
    private $client;
    private $em;


    public function __construct($client, $em)
    {
        $this->client = $client;
        $this->em = $em;
    }

    public function processUploadedDataFromBSolClient()
    {
        return true;
    }
}
