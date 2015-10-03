<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Provides a changelog decorator for managers.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_Decorator_Changelog
	extends MShop_Common_Manager_Decorator_Base
{
	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Iface $item, $fetch = true )
	{
		$manager = $this->getManager();

		$manager->saveItem( $item, $fetch );
		$new = $manager->getItem( $item->getId() );

		$this->getContext()->getLogger()->log( json_encode( $new->toArray() ), MW_Logger_Base::NOTICE, 'changelog' );
	}
}