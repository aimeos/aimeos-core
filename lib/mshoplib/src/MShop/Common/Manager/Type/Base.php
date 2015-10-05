<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Type;


/**
 * Abstract type manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Type\Iface
{
	private $prefix;
	private $context;
	private $config;
	private $searchConfig;


	/**
	 * Creates the type manager using the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 *
	 * @throws \Aimeos\MShop\Exception if no configuration is available
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$conf = $context->getConfig();
		$confpath = $this->getConfigPath();
		$this->config = array(
			'insert' => $conf->get( $confpath . 'insert' ),
			'update' => $conf->get( $confpath . 'update' ),
			'delete' => $conf->get( $confpath . 'delete' ),
			'search' => $conf->get( $confpath . 'search' ),
			'count' => $conf->get( $confpath . 'count' ),
			'newid' => $conf->get( $confpath . 'newid' ),
		);

		$this->searchConfig = $this->getSearchConfig();

		$required = array( 'count', 'delete', 'insert', 'newid', 'search', 'update' );
		$isList = array_keys( $this->config );

		foreach( $required as $key )
		{
			if( !in_array( $key, $isList ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Configuration of necessary SQL statement for "%1$s" not available', $key ) );
			}
		}

		parent::__construct( $context );

		$this->context = $context;

		if( ( $entry = reset( $this->searchConfig ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		if( ( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}
	}


	/**
	 * Creates new type item object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Type\Iface New type item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->context->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Common\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( substr( $this->prefix, 0, strlen( $this->prefix ) - 1 ) );
		}

		return parent::createSearch();
	}


	/**
	 * Adds or updates a type item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Type\Iface $item Type item object which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Common\\Item\\Type\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( $item->isModified() === false ) { return; }

		$dbm = $this->context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$sql = $this->config['insert'];
			} else {
				$sql = $this->config['update'];
			}

			$statement = $conn->create( $sql );

			$statement->bind( 1, $this->context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 2, $item->getCode(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 3, $item->getDomain(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 4, $item->getLabel(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 5, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 6, date( 'Y-m-d H:i:s', time() ) ); //mtime
			$statement->bind( 7, $this->context->getEditor() );

			if( $id !== null ) {
				$statement->bind( 8, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$statement->bind( 8, date( 'Y-m-d H:i:s', time() ) ); //ctime
			}

			$statement->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$item->setId( $this->newId( $conn, $this->config['newid'] ) );
				} else {
					$item->setId( $id ); // modified false
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->deleteItemsBase( $ids, $this->config['delete'] );
	}


	/**
	 * Returns the type item specified by its ID
	 *
	 * @param integer $id Id of type item object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Returns the type item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		$conf = reset( $this->searchConfig );

		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', $conf['code'], $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Type item with ID "%1$s" in "%2$s" not found', $id, $conf['code'] ) );
		}

		return $item;
	}


	/**
	 * Searches for all type items matching the given critera.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $search Search object with conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of type items implementing \Aimeos\MShop\Common\Item\Type\Iface
	 * @throws \Aimeos\MShop\Common\Exception if creating items failed
	 */
	public function searchItems( \Aimeos\MW\Common\Criteria\Iface $search, array $ref = array(), &$total = null )
	{
		$items = array();

		$dbm = $this->context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new \Aimeos\MShop\Exception( 'No configuration available.' );
			}

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->config['search'];
			$cfgPathCount = $this->config['count'];
			$required = array( trim( $this->prefix, '.' ) );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row['id']] = $this->createItemBase( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Creates a new manager for type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'common', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	abstract protected function getConfigPath();


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	abstract protected function getSearchConfig();


	/**
	 * Creates new type item object.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return \Aimeos\MShop\Common\Item\Type\Standard New type item object
	 */
	protected function createItemBase( array $values = array() )
	{
		return new \Aimeos\MShop\Common\Item\Type\Standard( $this->prefix, $values );
	}


	protected function getPrefix()
	{
		return $this->prefix;
	}
}
