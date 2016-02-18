<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Product\Category\Decorator;


/**
 * Cache cleanup decorator for product category JQAdm client
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Cache extends \Aimeos\Admin\JQAdm\Common\Decorator\Base
{
	/**
	 * Clears the cache after saving the item
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function save()
	{
		$result = $this->getClient()->save();
		$ids = array( 'catalog' );

		foreach( $this->getView()->param( 'category/catalog.id', array() ) as $id ) {
			$ids[] = 'catalog-' . $id;
		}

		$this->getContext()->getCache()->deleteByTags( $ids );

		return $result;
	}
}
