<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @param \Aimeos\MShop\Common\Item\Iface $items Item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Updated item including the generated ID
	 */
	public function save( $items, bool $fetch = true )
	{
		$items = $this->getManager()->save( $items, true );

		$this->getContext()->getLogger()->log( map( $items )->toJson(), \Aimeos\MW\Logger\Base::NOTICE, 'core/changelog' );

		return $items;
	}
}
