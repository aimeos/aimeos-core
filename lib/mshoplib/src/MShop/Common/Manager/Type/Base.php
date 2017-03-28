<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		parent::__construct( $context );

		$this->searchConfig = $this->getSearchConfig();

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
		$values = array(
			$this->prefix . 'siteid' => $this->getContext()->getLocale()->getSiteId(),
		);
		return $this->createItemBase( $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
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

		$context = $this->getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$type = 'insert';
			} else {
				$type = 'update';
			}

			$statement = $conn->create( $this->getSqlConfig( $this->getConfigPath() . $type ) );

			$statement->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 2, $item->getCode(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 3, $item->getDomain(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 4, $item->getLabel(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 5, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 6, date( 'Y-m-d H:i:s', time() ) ); //mtime
			$statement->bind( 7, $context->getEditor() );

			if( $id !== null ) {
				$statement->bind( 8, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$statement->bind( 8, date( 'Y-m-d H:i:s', time() ) ); //ctime
			}

			$statement->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$item->setId( $this->newId( $conn, $this->getConfigPath() . 'newid' ) );
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
		$this->deleteItemsBase( $ids, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = [], $domain = 'product', $type = null )
	{
		$find = array(
			$this->prefix . 'code' => $code,
			$this->prefix . 'domain' => $domain,
		);
		return $this->findItemBase( $find, $ref );
	}


	/**
	 * Returns the type item specified by its ID
	 *
	 * @param integer $id ID of type item object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Returns the type item of the given ID
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( $this->prefix . 'id', $id, $ref, $default );
	}


	/**
	 * Searches for all type items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of type items implementing \Aimeos\MShop\Common\Item\Type\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = [];

		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new \Aimeos\MShop\Exception( 'No configuration available.' );
			}

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';
			$required = array( trim( $this->prefix, '.' ) );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row[$this->prefix . 'id']] = $this->createItemBase( $row );
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
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Common\Item\Type\Standard( $this->prefix, $values );
	}


	/**
	 * Returns the prefix used for the item keys.
	 *
	 * @return string Item key prefix
	 */
	protected function getPrefix()
	{
		return $this->prefix;
	}
}
