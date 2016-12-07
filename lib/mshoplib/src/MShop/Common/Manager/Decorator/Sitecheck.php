<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a site check decorator for managers.
 *
 * @package MShop
 * @subpackage Common
 */
class Sitecheck
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		if( $item->getId() !== null && $item->isModified() )
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
			throw new \Aimeos\MShop\Exception( sprintf( 'Item can not be deleted. Site ID of item differs from present site ID.' ) );
		}

		parent::deleteItem( $id );
	}
}
