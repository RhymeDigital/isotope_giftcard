<?php

/**
 * Copyright (C) 2014 HB Agency
 * 
 * @author		Blair Winans <bwinans@hbagency.com>
 * @author		Adam Fisher <afisher@hbagency.com>
 * @link		http://www.hbagency.com
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace HBAgency\Hooks\AddProductToCollection;

use HBAgency\Model\Product\GiftCard;


class PreventMultipleGiftCards extends \Frontend
{
	
	
	/**
	 * Prevent multiple gift cards from being added to the cart because
	 * of how ProductCollectionItem calculates its price
	 * 
	 * Namespace:	Contao
	 * Class:		IsotopeProductCollection
	 * Method:		addProduct
	 * Hook:		$GLOBALS['ISO_HOOKS']['addProductToCollection']
	 *
	 * @access		public
	 * @param		integer
	 * @return		float
	 */
	public function run($objProduct, $intQuantity, $objCollection)
	{
		if ($objProduct instanceof GiftCard)
		{
			$objItems = (array)$objCollection->getItems();
			
			foreach ($objItems as $objItem)
			{
				if ($objItem->hasProduct() && $objItem->getProduct() instanceof GiftCard)
				{
					$_SESSION['ISO_ERROR'][] = 'Only one gift card amount can be purchased at a time.';
					return false;
				}
			}
		}
		
		return $intQuantity;
	}
}
