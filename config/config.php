<?php

/**
 * Copyright (C) 2014 HB Agency
 * 
 * @author		Blair Winans <bwinans@hbagency.com>
 * @author		Adam Fisher <afisher@hbagency.com>
 * @link		http://www.hbagency.com
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */



/**
 * Product classes
 */
\Isotope\Model\Product::registerModelType('giftcard', 'HBAgency\Model\Product\GiftCard');



/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['addProductToCollection'][]			= array('HBAgency\Hooks\AddProductToCollection\PreventMultipleGiftCards', 'run');