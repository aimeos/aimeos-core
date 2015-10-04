<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Cache
 */


/**
 * Null cache manager implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class MAdmin_Cache_Manager_None
	extends MAdmin_Common_Manager_Base
	implements MAdmin_Cache_Manager_Iface
{
	private $searchConfig = array(
		'cache.id' => array(
			'code' => 'cache.id',
			'internalcode' => '"id"',
			'label' => 'Cache ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Base::PARAM_STR,
		),
	);


	/**
	 * Returns the cache object
	 *
	 * @return MW_Cache_Iface Cache object
	 */
	public function getCache()
	{
		return MW_Cache_Factory::createManager( 'None', array(), null );
	}


	/**
	 * Create new cache item object.
	 *
	 * @return MAdmin_Cache_Item_Iface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->getContext()->getLocale()->getSiteId() );

		return new MAdmin_Cache_Item_Standard( $values );
	}


	/**
	 * Adds a new cache to the storage.
	 *
	 * @param MAdmin_Cache_Item_Iface $item Cache item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Iface $item, $fetch = true )
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
	 * @return MAdmin_Cache_Item_Iface Returns the cache item of the given id
	 * @throws MAdmin_Cache_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		throw new MAdmin_Cache_Exception( sprintf( 'Operation not supported' ) );
	}


	/**
	 * Search for cache entries based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Iface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of cache items implementing MAdmin_Cache_Item_Iface
	 */
	public function searchItems( MW_Common_Criteria_Iface $search, array $ref = array(), &$total = null )
	{
		return array();
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$path = 'classes/cache/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array(), $withsub );
	}


	/**
	 * Returns a new manager for cache extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'cache', $manager, $name );
	}
}
