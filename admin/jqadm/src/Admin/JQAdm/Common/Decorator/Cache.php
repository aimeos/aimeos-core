<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Decorator;


/**
 * Cache cleanup decorator for JQAdm clients
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Cache extends Base
{
	/**
	 * Clears the cache after saving the item
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function save()
	{
		$result = $this->getClient()->save();
		$item = $this->getView()->item;

		if( $item->getId() !== null )
		{
			$idtag = $item->getResourceType() . '-' . $item->getId();
			$this->getContext()->getCache()->deleteByTags( array( $item->getResourceType(), $idtag ) );
		}

		return $result;
	}
}
