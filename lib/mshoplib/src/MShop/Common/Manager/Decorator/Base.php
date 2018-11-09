<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Common\Manager\Iface $manager, \Aimeos\MShop\Context\Item\Iface $context )
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
		return call_user_func_array( array( $this->manager, $name ), $param );
	}


	/**
	 * Removes old entries from the storage
	 *
	 * @param array $siteids List of IDs for sites Whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$this->manager->cleanup( $siteids );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param string|null Type the item should be created with
	 * @param string|null Domain of the type the item should be created with
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Iface New item object
	 */
	public function createItem( $type = null, $domain = null, array $values = [] )
	{
		return $this->manager->createItem( $type, $domain, $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
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
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param boolean $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = [], $domain = 'product', $type = null, $default = false )
	{
		return $this->manager->findItem( $code, $ref, $domain, $type, $default );
	}


	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Unique ID of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->manager->getItem( $id, $ref, $default );
	}

	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		return $this->manager->getResourceType( $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return $this->manager->getSearchAttributes( $withsub );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
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
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		return $this->manager->saveItem( $item, $fetch );
	}


	/**
	 * Adds or updates a list of item objects.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[] $items List of item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface[] Saved item objects
	 */
	public function saveItems( array $items, $fetch = true )
	{
		return $this->manager->saveItems( $items, $fetch );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		return $this->manager->searchItems( $search, $ref, $total );
	}


	/**
	 * Search for all referenced items from the list based on the given critera.
	 *
	 * Only criteria from the list and list type can be used for searching and
	 * sorting, but no criteria from the referenced items.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array Associative list of domains as keys and lists with pairs of IDs and items implementing \Aimeos\MShop\Common\Item\Iface
	 * @throws \Aimeos\MShop\Exception if creating items failed
	 * @see \Aimeos\MW\Criteria\SQL
	 */
	public function searchRefItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		return $this->manager->searchRefItems( $search, $ref, $total );
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object )
	{
		parent::setObject( $object );

		$this->manager->setObject( $object );

		return $this;
	}


	/**
	 * Starts a database transaction on the connection identified by the given name.
	 */
	public function begin()
	{
		$this->manager->begin();
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name.
	 */
	public function commit()
	{
		$this->manager->commit();
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name.
	 */
	public function rollback()
	{
		$this->manager->rollback();
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