<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Decorator;


/**
 * Index rebuild decorator for JQAdm clients
 *
 * @package Client
 * @subpackage JQAdm
 */
class Index extends Base
{
	/**
	 * Rebuilds the index after saving the item
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function save()
	{
		$result = $this->getClient()->save();
		$item = $this->getView()->item;

		\Aimeos\MShop\Factory::createManager( $this->getContext(), 'index' )->saveItem( $item );

		return $result;
	}
}
