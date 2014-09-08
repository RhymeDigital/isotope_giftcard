<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://isotopeecommerce.org
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace HBAgency\Model;

use Isotope\Interfaces\IsotopePrice;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Product;
use Isotope\Model\ProductPrice;
use Isotope\Model\ProductCollectionItem;


/**
 * ProductPrice defines an advanced price of a product
 *
 * @copyright  Isotope eCommerce Workgroup 2009-2013
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 */
class GiftCardProductPrice extends ProductPrice implements IsotopePrice
{
	
	/**
	 * The attribute name of the gift card amount
	 * @var string
	 */
	protected $strField = 'gift_card_amount';
	
	

    /**
     * Construct the object
     * @param   array
     * @param   array
     * @param   boolean
     */
    public function __construct(\Database\Result $objResult = null)
    {
        parent::__construct($objResult);

        /*$objTiers = \Database::getInstance()->prepare("SELECT * FROM tl_iso_product_pricetier WHERE pid=? ORDER BY min")->execute($objResult->id);

        while ($objTiers->next()) {
            $this->arrTiers[$objTiers->min] = $objTiers->price;
        }*/
        
        $this->arrTiers[1] = $this->getGiftCardAmount();
    }

    /**
     * Return price
     * @param   int
     * @return  float
     */
    public function getAmount($intQuantity = 1)
    {
        return Isotope::calculatePrice($this->getGiftCardAmount($intQuantity), $this, 'price', $this->tax_class);
    }

    /**
     * Return original price
     * @param   int
     * @return  float
     */
    public function getOriginalAmount($intQuantity = 1)
    {
        return Isotope::calculatePrice($this->getGiftCardAmount($intQuantity), $this, 'original_price', $this->tax_class);
    }

    /**
     * Return net price (without taxes)
     * @param   int
     * @return  float
     */
    public function getNetAmount($intQuantity = 1)
    {
        $fltAmount = $this->getGiftCardAmount($intQuantity);

        /** @var \Isotope\Model\TaxClass $objTaxClass */
        if (($objTaxClass = $this->getRelated('tax_class')) !== null) {
            $fltAmount = $objTaxClass->calculateNetPrice($fltAmount);
        }

        return Isotope::calculatePrice($fltAmount, $this, 'net_price');
    }

    /**
     * Return gross price (with all taxes)
     * @param   int
     * @return  float
     */
    public function getGrossAmount($intQuantity = 1)
    {
        $fltAmount = $this->getGiftCardAmount($intQuantity);

        /** @var \Isotope\Model\TaxClass $objTaxClass */
        if (($objTaxClass = $this->getRelated('tax_class')) !== null) {
            $fltAmount = $objTaxClass->calculateGrossPrice($fltAmount);
        }

        return Isotope::calculatePrice($fltAmount, $this, 'gross_price');
    }

    /**
     * Get lowest amount of all tiers
     * @return  float
     */
    public function getLowestAmount()
    {
        if (!$this->hasTiers()) {
            return $this->getAmount();
        }

        return Isotope::calculatePrice(min($this->arrTiers), $this, 'price', $this->tax_class);
    }
    
    
    protected function getGiftCardAmount($intQuantity=1)
    {
        if (\Input::post($this->strField))
        {
        	\Controller::loadDataContainer(Product::getTable());
	        $arrData = $GLOBALS['TL_DCA'][Product::getTable()]['fields'][$this->strField];
			$arrData['eval']['required']  = $arrData['eval']['mandatory'];
			
	        $objWidget = new \FormTextField(\Widget::getAttributesFromDca($arrData, $this->strField));
	        $objWidget->validate();
	        
	        if (!$objWidget->hasErrors())
	        {
	        	// todo: use Isotope utility methods to format this properly
	        	$fltPrice = round((floatval($objWidget->value) * $intQuantity), 2);
		        return $fltPrice;
	        }
        }
        else
        {
        	$objItem = ProductCollectionItem::findOneBy(array('pid=?', 'product_id=?'), array(Isotope::getCart()->id, $this->pid));
        	
        	if ($objItem)
        	{
	        	$arrOptions = $objItem->getOptions();
	        	// todo: use Isotope utility methods to format this properly
	        	$fltPrice = round((floatval($arrOptions[$this->strField]) * $intQuantity), 2);
		        return $fltPrice;
        	}
        	else
        	{
	        	return parent::getAmount($intQuantity);
        	}
        }
        
        return 0.00;
    }
}
