<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Provides a site check decorator for managers.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_Decorator_Sitecheck
	extends MShop_Common_Manager_Decorator_Base
{
	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		if( $item->getId() !== null )
		{
			$actual = $this->getManager()->getItem( $item->getId() )->getSiteId();
			$siteId = $this->getContext()->getLocale()->getSiteId();

			if( $actual !== null && $actual != $siteId ) {
				return;
			}
		}

		parent::saveItem( $item, $fetch );
	}


	/**
	 * Deletes an item object.
	 *
	 * @param integer $id Unique ID of the item
	 */
	public function deleteItem( $id )
	{
		$actual = $this->getManager()->getItem( $id )->getSiteId();
		$siteId = $this->getContext()->getLocale()->getSiteId();

		if( $actual !== null && $actual != $siteId ) {
			throw new MShop_Exception( sprintf( 'Item can not be deleted. Site ID of item differs from present site ID.' ) );
		}

		parent::deleteItem( $id );
	}
}
