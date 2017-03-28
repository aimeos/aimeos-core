<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MAdmin
 * @subpackage Cache
 */


namespace Aimeos\MAdmin\Cache\Manager;


/**
 * Null cache manager implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class None
	extends \Aimeos\MAdmin\Common\Manager\Base
	implements \Aimeos\MAdmin\Cache\Manager\Iface
{
	private $searchConfig = array(
		'cache.id' => array(
			'code' => 'cache.id',
			'internalcode' => '"id"',
			'label' => 'Cache ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
	);


	/**
	 * Returns the cache object
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function getCache()
	{
		return \Aimeos\MW\Cache\Factory::createManager( 'None', [], null );
	}


	/**
	 * Create new cache item object.
	 *
	 * @return \Aimeos\MAdmin\Cache\Item\Iface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->getContext()->getLocale()->getSiteId() );

		return new \Aimeos\MAdmin\Cache\Item\Standard( $values );
	}


	/**
	 * Adds a new cache to the storage.
	 *
	 * @param \Aimeos\MAdmin\Cache\Item\Iface $item Cache item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
	}


	/**
	 * Creates the cache object for the given cache id.
	 *
	 * @param integer $id Cache ID to fetch cache object for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Returns the cache item of the given id
	 * @throws \Aimeos\MAdmin\Cache\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		throw new \Aimeos\MAdmin\Cache\Exception( sprintf( 'Operation not supported' ) );
	}


	/**
	 * Search for cache entries based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of cache items implementing \Aimeos\MAdmin\Cache\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		return [];
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'madmin/cache/manager/submanagers';

		return $this->getResourceTypeBase( 'cache', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$path = 'madmin/cache/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for cache extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'cache', $manager, $name );
	}
}
