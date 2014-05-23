<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Default coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Manager_Default
	extends MShop_Coupon_Manager_Abstract
	implements MShop_Coupon_Manager_Interface
{
	private $_searchConfig = array(
		'coupon.id'=> array(
			'code'=>'coupon.id',
			'internalcode'=>'mcou."id"',
			'label'=>'Coupon ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'coupon.siteid'=> array(
			'code'=>'coupon.siteid',
			'internalcode'=>'mcou."siteid"',
			'label'=>'Coupon site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.label'=> array(
			'code'=>'coupon.label',
			'internalcode'=>'mcou."label"',
			'label'=>'Coupon label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.provider'=> array(
			'code'=>'coupon.provider',
			'internalcode'=>'mcou."provider"',
			'label'=>'Coupon method',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.config'=> array(
			'code'=>'coupon.config',
			'internalcode'=>'mcou."config"',
			'label'=>'Coupon config',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.datestart'=> array(
			'code'=>'coupon.datestart',
			'internalcode'=>'mcou."start"',
			'label'=>'Coupon start date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.dateend'=> array(
			'code'=>'coupon.dateend',
			'internalcode'=>'mcou."end"',
			'label'=>'Coupon end date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.status'=> array(
			'code'=>'coupon.status',
			'internalcode'=>'mcou."status"',
			'label'=>'Coupon status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'coupon.ctime'=> array(
			'code'=>'coupon.ctime',
			'internalcode'=>'mcou."ctime"',
			'label'=>'Coupon create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.mtime'=> array(
			'code'=>'coupon.mtime',
			'internalcode'=>'mcou."mtime"',
			'label'=>'Coupon modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.editor'=> array(
			'code'=>'coupon.editor',
			'internalcode'=>'mcou."editor"',
			'label'=>'Coupon editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-coupon' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/coupon/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'code' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/coupon/manager/default/item/delete' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			foreach( $this->_getContext()->getConfig()->get( 'classes/coupon/manager/submanagers', array( 'code' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Creates a new empty coupon item instance
	 *
	 * @return MShop_Coupon_Item_Interface Creates a blank coupon item
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns the coupons item specified by its ID.
	 *
	 * @param integer $couponId Unique ID of the coupon item in the storage
	 * @return MShop_Coupon_Item_Interface Returns the coupon item of the given id
	 * @throws MShop_Coupon_Exception If coupon couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'coupon.id', $id, $ref );
	}


	/**
	 * Saves a coupon item to the storage.
	 *
	 * @param MShop_Coupon_Item_Interface $coupon Coupon implementing the coupon interface
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Coupon_Exception If coupon couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Interface $coupon, $fetch = true )
	{
		$iface = 'MShop_Coupon_Item_Interface';
		if( !( $coupon instanceof $iface ) ) {
			throw new MShop_Coupon_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$coupon->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $coupon->getId();

			$path = 'mshop/coupon/manager/default/item/';
			$path .= ( $id === null ? 'insert' : 'update' );

			$stmt = $this->_getCachedStatement($conn, $path);

			$stmt->bind( 1, $context->getLocale()->getSiteId() );
			$stmt->bind( 2, $coupon->getLabel() );
			$stmt->bind( 3, $coupon->getProvider() );
			$stmt->bind( 4, json_encode( $coupon->getConfig() ), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 5, $coupon->getDateStart() );
			$stmt->bind( 6, $coupon->getDateEnd() );
			$stmt->bind( 7, $coupon->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind( 8, date('Y-m-d H:i:s', time()) );// mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null) {
				$stmt->bind(10, $coupon->getId(), MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(10, date('Y-m-d H:i:s', time()) );// ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/coupon/manager/default/item/newid';
					$coupon->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$coupon->setId( $id );
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
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
		$path = 'mshop/coupon/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array Returns a list of coupon items implementing MShop_Coupon_Item_Interface
	 *
	 * @throws MW_DB_Exception On failures with the db object
	 * @throws MShop_Common_Exception On failures with the MW_Common_Criteria_ object
	 * @throws MShop_Coupon_Exception On failures with the coupon items
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;
			$cfgPathSearch = 'mshop/coupon/manager/default/item/search';
			$cfgPathCount =  'mshop/coupon/manager/default/item/count';
			$required = array( 'coupon' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					$config = $row['config'];

					if ( ( $row['config'] = json_decode( $row['config'], true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_locale.config', $row['id'], $config );
						$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );
					}

					$items[ $row['id'] ] = $this->_createItem( $row );
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'coupon', $manager, $name );
	}


	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param MShop_Coupon_Item_Interface $item Coupon item interface
	 * @return MShop_Coupon_Provider_Interface Returns a coupon provider instance
	 * @throws MShop_Coupon_Exception If coupon couldn't be found
	 */
	public function getProvider( MShop_Coupon_Item_Interface $item, $code )
	{
		$names = explode( ',', $item->getProvider() );

		if( ( $providername = array_shift( $names ) ) === null ){
			throw new MShop_Coupon_Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if ( ctype_alnum( $providername ) === false ) {
			throw new MShop_Coupon_Exception( sprintf( 'Invalid characters in provider name "%1$s"', $providername ) );
		}

		$interface = 'MShop_Coupon_Provider_Factory_Interface';
		$classname = 'MShop_Coupon_Provider_' . $providername;

		if( class_exists( $classname ) === false ) {
			throw new MShop_Coupon_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->_getContext();
		$provider = new $classname( $context, $item, $code );

		if( ( $provider instanceof $interface ) === false )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
			throw new MShop_Coupon_Exception( $msg );
		}

		$decorators = $context->getConfig()->get( 'mshop/coupon/provider/decorators', array() );

		$object = $this->_addCouponDecorators( $item, $code, $provider, $names );
		$object = $this->_addCouponDecorators( $item, $code, $object, $decorators );
		$object->setObject( $object );

		return $object;
	}


	/**
	 * Creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->_createSearch( 'coupon' );
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr = array();
			$expr[] = $object->getConditions();

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.datestart', null );
			$temp[] = $object->compare( '<=', 'coupon.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.dateend', null );
			$temp[] = $object->compare( '>=', 'coupon.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new coupon item instance
	 *
	 * @param array $data Optional values to set
	 * @return MShop_Coupon_Item_Default Returns a new created coupon item instance
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Coupon_Item_Default( $values );
	}
}