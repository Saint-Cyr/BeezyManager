<?php
/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) StCyrLabs <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace KmBundle\Service;

use KmBundle\Entity\Branch;
use TransactionBundle\Entity\Product;
use TransactionBundle\Entity\Stock;
use Doctrine\Common\Collections\ArrayCollection;

class ReportHandler
{
    //To store the entity manager
    private $em;
    
    public function __construct($em) 
    {
        $this->em = $em;
    }
    
    
    /**
     * 
     * @param \DateTime $initialDate
     * @param \DateTime $finalDate
     * @param array $productToBeReported
     * this method generate one of the types of report (ReportA). it is suitable for business like cafe internet...
     * it build a table with a flexible number of culumn where each culumn can represent a product or a set of
     * product. See the Super-admin Guid for more information
     */
    public function getReportA(\DateTime $initialDate, \DateTime $finalDate, array $productToBeReported = null, array $productToBeUnreported = null)
    {
        $reportedProducts = array();
        //Base on configuration, some product must not be part of the report but for now it's null;
        $unreportedProducts = $productToBeUnreported;
        //Group all the reported product before put them in the output variable
        $temp = array();
        if($productToBeReported){
            foreach ($productToBeReported as $p){
                //Build the reportA for this particular product and push it in the output variable ($reportedProducts)
                $temp[] = $this->buildReportAoneProduct($initialDate, $finalDate, $p);
                //Remove this product from the list of unreportedProduct
                $this->removeOneProduct($unreportedProducts, $p);
            }
        }else{
            $temp = null;
        }
        //Build the total for sale and profit for both E1 & E2
        $reportedProducts['E2_SALE'] = $this->getTotalSale($temp);
        $reportedProducts['E2_PROFIT'] = $this->getTotalProfit($temp);
        $reportedProducts['E2'] = $temp;
        //Remove the products that must not be considered in the report
        $this->removeManyProducts($unreportedProducts, $productToBeUnreported);
        //Build the last column of the table of the report with the unselected products 
        //(that are not part of $productToBeUnreported)
        $reportedProducts['E1'] = $this->buildReportAmanyProducts($initialDate, $finalDate, $unreportedProducts);
        $reportedProducts['E1_SALE'] = $this->getTotalSale($reportedProducts['E1']);
        $reportedProducts['E1_PROFIT'] = $this->getTotalProfit($reportedProducts['E1']);
        
        return $reportedProducts;
    }
    
    public function getTotalSale($products)
    {
        $sale = null;
        if($products){
            
            foreach($products as $p){
                $sale = $sale + $p->getFlyAmount();
            }
        }
        
        return $sale;
    }
    
    public function getTotalProfit($products)
    {
        $profit = null;
        if($products){
            
            foreach($products as $p){
                $profit = $profit + $p->getFlyProfit();
            }
        }
        
        return $profit;
    }
    
    public function buildReportAoneProduct(\DateTime $initialDate, \DateTime $finalDate, Product $product)
    {   
        $sales = $this->em->getRepository('TransactionBundle:Sale')
                      ->getFromTo($initialDate, $finalDate, $product);
        
        $profit = null;
        $amount = null;
        
        foreach ($sales as $s){
            $profit = $profit + $s->getProfit();
            $amount = $amount + $s->getAmount();
        }
        
        $product->setFlyProfit($profit);
        $product->setFlyAmount($amount);
        
        return $product;
    }
    
    /*
     * @return array of hydrated products with all the nacessary information like amount, profit,...
     */
    public function buildReportAmanyProducts(\DateTime $initialDate, \DateTime $finalDate, array $unreportedProducts = null)
    {
        $results = array();
        if($unreportedProducts){
            foreach ($unreportedProducts as $up){
                $sales = $this->em->getRepository('TransactionBundle:Sale')
                          ->getFromTo($initialDate, $finalDate, $up);

                $profit = null;
                $amount = null;

                foreach ($sales as $s){
                    $profit = $profit + $s->getProfit();
                    $amount = $amount + $s->getAmount();
                }

                $up->setFlyProfit($profit);
                $up->setFlyAmount($amount);
            }
        }
        
        
        return $unreportedProducts;
    }
    
    /*
     * Remove a product in the array of products
     */
    public function removeOneProduct(array $unreportedProducts = null, Product $product)
    {
        if($unreportedProducts){
            
            foreach ($unreportedProducts as $key => $value){
                if($value == $product){
                    array_splice($unreportedProducts, $key, 1);
                }
            }
        }
        
        return $unreportedProducts;
    }
    
    /*
     * Remove a set of product from the list of unreported products
     */
    public function removeManyProducts(array $unreportedProducts = null, array $productsToBeUnreported = null)
    {
        if($unreportedProducts){
            
            $unreportedProducts = array_udiff($unreportedProducts, $productsToBeUnreported,
                function($object_a, $object_b){
                    return $object_a->getBarcode() - $object_b->getBarcode();
                }
            );
        }
        
        return $unreportedProducts;
    }
    
    public function getProfitOnFly(array $stransactions)
    {
        $profit = null;
        
        foreach ($stransactions as $st){
            $profit = $profit + $st->getProfit();
        }
        
        return $profit;
    }
    
    public function getSaleAmountOnFly(array $stransactions)
    {
        $saleAmount = null;
        
        foreach ($stransactions as $st){
            $saleAmount = $saleAmount + $st->getTotalAmount();
        }
        
        return $saleAmount;
    }
}
