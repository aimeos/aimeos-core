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
 * Provides common methods for manager decorators.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Decorator\Iface
{
	private \Aimeos\MShop\Common\Manager\Iface $manager;


	/**
	 * Initializes the manager decorator.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Common\Manager\Iface $manager, \Aimeos\MShop\ContextIface $context )
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
	public function __call( string $name, array $param )
	{
		return call_user_func_array( array( $this->manager, $name ), $param );
	}


	/**
	 * Adds a filter callback for an item type
	 *
	 * @param string $iface Interface name of the item to apply the filter to
	 * @param \Closure $fcn Anonymous function receiving the item to check as first parameter
	 */
	public function addFilter( string $iface, \Closure $fcn )
	{
		$this->manager->addFilter( $iface, $fcn );
	}


	/**
	 * Returns the class names of the manager and used decorators.
	 *
	 * @return array List of class names
	 */
	public function classes() : array
	{
		return array_merge( $this->getManager()->classes(), [get_class( $this )] );
	}


	/**
	 * Removes old entries from the storage
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->manager->clear( $siteids );
		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Iface New item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->manager->create( $values );
	}


	/**
	 * Creates a new cursor based on the filter criteria
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @return \Aimeos\MShop\Common\Cursor\Iface Cursor object
	 */
	public function cursor( \Aimeos\Base\Criteria\Iface $filter ) : \Aimeos\MShop\Common\Cursor\Iface
	{
		return $this->manager->cursor( $filter );
	}


	/**
	 * Deletes one or more items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|array|string $items Item object, ID of the item or a list of them
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->manager->delete( $items );
		return $this;
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->manager->filter( $default, $site );
	}

	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], ?string $domain = 'product', string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->manager->find( $code, $ref, $domain, $type, $default );
	}


	/**
	 * Returns the item specified by its ID
	 *
	 * @param string $id Id of item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->manager->get( $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ): array
	{
		return $this->manager->getResourceType( $withsub );
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return $this->manager->getSaveAttributes();
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
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
	public function getSubManager( string $domain, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->manager->getSubManager( $domain, $name );
	}

	/**
	 * Iterates over all matched items and returns the found ones
	 *
	 * @param \Aimeos\MShop\Common\Cursor\Iface $cursor Cursor object with filter, domains and cursor
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @return \Aimeos\Map|null List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function iterate( \Aimeos\MShop\Common\Cursor\Iface $cursor, array $ref = [] ) : ?\Aimeos\Map
	{
		return $this->manager->iterate( $cursor, $ref );
	}


	/**
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		return $this->manager->save( $items, $fetch );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $filter, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		return $this->manager->search( $filter, $ref, $total );
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object ) : \Aimeos\MShop\Common\Manager\Iface
	{
		parent::setObject( $object );

		$this->manager->setObject( $object );

		return $this;
	}


	/**
	 * Starts a database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function begin() : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->manager->begin();
		return $this;
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function commit() : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->manager->commit();
		return $this;
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rollback() : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->manager->rollback();
		return $this;
	}



	/**
	 * Returns the manager object
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->manager;
	}
}
