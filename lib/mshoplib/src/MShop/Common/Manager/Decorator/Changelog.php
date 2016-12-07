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
 * Provides a changelog decorator for managers.
 *
 * @package MShop
 * @subpackage Common
 */
class Changelog
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
		$manager = $this->getManager();

		$manager->saveItem( $item, $fetch );
		$new = $manager->getItem( $item->getId() );

		$this->getContext()->getLogger()->log( json_encode( $new->toArray() ), \Aimeos\MW\Logger\Base::NOTICE, 'changelog' );
	}
}