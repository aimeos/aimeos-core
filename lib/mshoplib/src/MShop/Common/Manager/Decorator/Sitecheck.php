<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	extends MShop_Common_Manager_Decorator_Abstract
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
			$actual = $this->_getManager()->getItem( $item->getId() );
			$siteId = $this->_getContext()->getLocale()->getSiteId();

			if( $actual->getSiteId() != $siteId ) {
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
		$actual = $this->_getManager()->getItem( $id );
		$siteId = $this->_getContext()->getLocale()->getSiteId();

		if( $actual->getSiteId() != $siteId ) {
			throw new MShop_Exception( sprintf( 'Item can not be deleted. Site ID of item differs from present site ID.' ) );
		}

		parent::deleteItem( $id );
	}
}
