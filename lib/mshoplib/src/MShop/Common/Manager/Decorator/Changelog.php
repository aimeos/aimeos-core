<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		$manager = $this->getManager();

		$item = $manager->saveItem( $item, $fetch );
		$new = $manager->getItem( $item->getId() );

		$this->getContext()->getLogger()->log( json_encode( $new->toArray() ), \Aimeos\MW\Logger\Base::NOTICE, 'changelog' );

		return $item;
	}
}
