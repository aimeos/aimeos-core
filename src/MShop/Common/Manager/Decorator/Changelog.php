<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	 * Deletes one or more items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items Item object, ID or a list of them
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->getManager()->delete( $items );

		if( !map( $items )->isEmpty() ) {
			$this->context()->logger()->notice( $items, 'changelog:delete' );
		}

		return $this;
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $items Item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Updated item including the generated ID
	 */
	public function save( $items, bool $fetch = true )
	{
		$log = map( $items )->isModified()->filter()->sum();

		$items = $this->getManager()->save( $items, true );

		if( $log ) {
			$this->context()->logger()->notice( $items, 'changelog:save' );
		}

		return $items;
	}
}
