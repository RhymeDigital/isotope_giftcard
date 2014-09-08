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

namespace HBAgency\Model\Product;

use Haste\Units\Mass\WeightAggregate;

use Isotope\Isotope;
use Isotope\Model\Product;
use Isotope\Model\Product\Standard as StandardProduct;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Model\ProductPrice;
use HBAgency\Model\GiftCardProductPrice;


/**
 * Class GiftCard
 *
 * Provide methods to handle Isotope products.
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Christian de la Haye <service@delahaye.de>
 */
class GiftCard extends StandardProduct implements IsotopeProduct, WeightAggregate
{

    /**
     * Set all product options
     * @return  void
     */
    public function setOptions($arrOptions)
    {
        $this->arrOptions = $arrOptions;
    }
    
    /**
     * Returns true if the product is available
     * @return  bool
     */
    public function isAvailableForCollection(IsotopeProductCollection $objCollection)
    {
        return true;
    }


    /**
     * Return a widget object based on a product attribute's properties
     * @param   string
     * @param   boolean
     * @return  string
     */
    protected function generateProductOptionWidget($strField, &$arrVariantOptions)
    {
    	\Controller::loadDataContainer(Product::getTable());
        $GLOBALS['TL_DCA'][Product::getTable()]['fields']['gift_card_amount']['default'] = Isotope::formatPrice($this->getPrice()->getAmount());
        
        return parent::generateProductOptionWidget($strField, $arrVariantOptions);
    }

    /**
     * Get product price model
     * @param   IsotopeProductCollection
     * @return  IsotopePrice
     */
    public function getPrice(IsotopeProductCollection $objCollection = null)
    {
        if (null !== $objCollection && $objCollection !== Isotope::getCart()) {
            return GiftCardProductPrice::findByProductAndCollection($this, $objCollection, array('return'=>'Model'));

        } elseif (false === $this->objPrice) {
            if (null === $objCollection) {
                $objCollection = Isotope::getCart();
            }

            $this->objPrice = GiftCardProductPrice::findByProductAndCollection($this, $objCollection, array('return'=>'Model'));
        }
        
        return $this->objPrice;
    }


}