<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides common methods for manager decorators.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Decorator\Iface
{
	private $manager;


	/**
	 * Initializes the manager decorator.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Common\Manager\Iface $manager )
	{
		parent::__construct( $context );
		$this->manager = $manager;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\MShop\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->manager, $name ), $param ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Method "%1$s" for provider not available', $name ) );
		}

		return $result;
	}


	/**
	 * Removes old entries from the storage
	 *
	 * @param array $siteids List of IDs for sites Whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		return $this->manager->cleanup( $siteids );
	}


	/**
	 * Creates new item object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface New item object
	 */
	public function createItem()
	{
		return $this->manager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->manager->createSearch( $default );
	}


	/**
	 * Deletes the item specified by its ID.
	 *
	 * @param integer $id ID of the item object
	 */
	public function deleteItem( $id )
	{
		$this->manager->deleteItem( $id );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->manager->deleteItems( $ids );
	}


	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Unique ID of the item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->manager->getItem( $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Common\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return $this->manager->getSearchAttributes( $withsub );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null )
	{
		return $this->manager->getSubManager( $domain, $name );
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$this->manager->saveItem( $item, $fetch );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Common\Criteria\Iface $search, array $ref = array(), &$total = null )
	{
		return $this->manager->searchItems( $search, $ref, $total );
	}


	/**
	 * Search for all referenced items from the list based on the given critera.
	 *
	 * Only criteria from the list and list type can be used for searching and
	 * sorting, but no criteria from the referenced items.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @throws \Aimeos\MShop\Exception if creating items failed
	 * @see \Aimeos\MW\Common\Criteria\SQL
	 */
	public function searchRefItems( \Aimeos\MW\Common\Criteria\Iface $search, array $ref = array(), &$total = null )
	{
		return $this->manager->searchRefItems( $search, $ref, $total );
	}



	/**
	 * Returns the manager object.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		return $this->manager;
	}
}