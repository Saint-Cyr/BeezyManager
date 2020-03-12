<?php
/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) 2017 Saint-Cyr <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace KmBundle\Service;

class BarcodeHandler
{
    //To store the entity manager
    private $em;
    
    public function __construct($em) 
    {
        $this->em = $em;
    }
    
    
    
    /**
     * 
     * @return int of
     * the length is $length 
     */
    public function generateCode($length = 13)
    {
        $result = array();
        
        $code = null;
        
        for($i = 0; $i < $length; $i++){
            $code = $code.mt_rand(0, 9);
        }
        
        return $code;
    }
    
    /**
     * 
     * @return int of
     * the length is $length 
     */
    public function generateBarcode($number = 13)
    {
        $number = $this->generateCode(8);
        
        $code = '200' . str_pad($number, 9, '0');
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit. 
        // loop backwards to make the loop length-agnostic. The same basic functionality 
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--)
        {
            $sum += (int)$code[$i] * ($weightflag?3:1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }
}
