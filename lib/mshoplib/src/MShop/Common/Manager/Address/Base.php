<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Address;


/**
 * Common abstract address manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	private $prefix;
	private $searchConfig;


	/**
	 * Initializes a new common address manager object using the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 *
	 * @throws \Aimeos\MShop\Exception if no configuration is available
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->searchConfig = $this->getSearchConfig();

		if( ( $entry = reset( $this->searchConfig ) ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Search configuration not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Search configuration for "%1$s" not available' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $entry['code'] ) );
		}

		if( ( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Search configuration for "%1$s" not available' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $entry['code'] ) );
		}
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Address\Iface New address item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values[$this->prefix . 'siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Common\Manager\Address\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->deleteItemsBase( $itemIds, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Returns the common address item object specificed by its ID.
	 *
	 * @param string $id Unique common address ID referencing an existing address
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Returns the address item of the given id
	 * @throws \Aimeos\MShop\Exception If address search configuration isn't available
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( ( $conf = reset( $this->searchConfig ) ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Address search configuration not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		return $this->getItemBase( $conf['code'], $id, $ref, $default );
	}


	/**
	 * Saves a common address item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item common address item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Address\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Address\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$path = $this->getConfigPath();
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null ) {
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path .= 'insert' ) );
			} else {
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path .= 'update' ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getCompany() );
			$stmt->bind( $idx++, $item->getVatId() );
			$stmt->bind( $idx++, $item->getSalutation() );
			$stmt->bind( $idx++, $item->getTitle() );
			$stmt->bind( $idx++, $item->getFirstname() );
			$stmt->bind( $idx++, $item->getLastname() );
			$stmt->bind( $idx++, $item->getAddress1() );
			$stmt->bind( $idx++, $item->getAddress2() );
			$stmt->bind( $idx++, $item->getAddress3() );
			$stmt->bind( $idx++, $item->getPostal() );
			$stmt->bind( $idx++, $item->getCity() );
			$stmt->bind( $idx++, $item->getState() );
			$stmt->bind( $idx++, $item->getCountryId() );
			$stmt->bind( $idx++, $item->getLanguageId() );
			$stmt->bind( $idx++, $item->getTelephone() );
			$stmt->bind( $idx++, $item->getEmail() );
			$stmt->bind( $idx++, $item->getTelefax() );
			$stmt->bind( $idx++, $item->getWebsite() );
			$stmt->bind( $idx++, $item->getLongitude(), \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT );
			$stmt->bind( $idx++, $item->getLatitude(), \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT );
			$stmt->bind( $idx++, $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getBirthday() );
			$stmt->bind( $idx++, $date ); //mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true ) {
				$id = $this->newId( $conn, $this->getConfigPath() . 'newid' );
			}

			$item->setId( $id );

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Address\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = [];

		try
		{
			$required = [trim( $this->prefix, '.' )];
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
					$items[$row[$this->prefix . 'id']] = $item;
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return map( $items );
	}


	/**
	 * Returns a new manager for address extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'common', 'address/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	abstract protected function getConfigPath() : string;


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	abstract protected function getSearchConfig() : array;


	/**
	 * Returns the search key and item prefix
	 *
	 * @return string Search key / item prefix
	 */
	protected function getPrefix() : string
	{
		return $this->prefix;
	}


	/**
	 * Creates a new address item
	 *
	 * @param array $values List of attributes for address item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface New address item
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Common\Item\Address\Iface
	{
		return new \Aimeos\MShop\Common\Item\Address\Standard( $this->prefix, $values );
	}
}
