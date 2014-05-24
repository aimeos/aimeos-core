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

class MShop_Coupon_Manager_Code_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Coupon_Manager_Code_Interface
{
	private $_searchConfig = array(
		'coupon.code.id'=> array(
			'code'=>'coupon.code.id',
			'internalcode'=>'mcouco."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_coupon_code" AS mcouco ON (mcou."id"=mcouco."couponid")' ),
			'label'=>'Coupon code ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.code.siteid'=> array(
			'code'=>'coupon.code.siteid',
			'internalcode'=>'mcouco."siteid"',
			'label'=>'Coupon code site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.code.couponid'=> array(
			'code'=>'coupon.code.couponid',
			'internalcode'=>'mcouco."couponid"',
			'label'=>'Coupon ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.code.code'=> array(
			'code'=>'coupon.code.code',
			'internalcode'=>'mcouco."code"',
			'label'=>'Coupon code value',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.count'=> array(
			'code'=>'coupon.code.count',
			'internalcode'=>'mcouco."count"',
			'label'=>'Coupon code quantity',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.datestart'=> array(
			'code'=>'coupon.code.datestart',
			'internalcode'=>'mcouco."start"',
			'label'=>'Coupon code start date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.dateend'=> array(
			'code'=>'coupon.code.dateend',
			'internalcode'=>'mcouco."end"',
			'label'=>'Coupon code end date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.ctime'=> array(
			'code'=>'coupon.code.ctime',
			'internalcode'=>'mcouco."ctime"',
			'label'=>'Coupon code create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.mtime'=> array(
			'code'=>'coupon.code.mtime',
			'internalcode'=>'mcouco."mtime"',
			'label'=>'Coupon code modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.editor'=> array(
			'code'=>'coupon.code.editor',
			'internalcode'=>'mcouco."editor"',
			'label'=>'Coupon code editor',
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
		$path = 'classes/coupon/manager/code/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/coupon/manager/code/default/item/delete' );
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
		/** classes/coupon/manager/code/name
		 * Class name of the used coupon code manager implementation
		 *
		 * Each default coupon code manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Coupon_Manager_Address_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Coupon_Manager_Address_Mycode
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/coupon/manager/code/name = Mycode
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAddress"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/coupon/manager/code/decorators/excludes
		 * Excludes decorators added by the "common" option from the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the coupon code manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/global
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/global
		 * Adds a list of globally available decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the coupon controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/local
		 * Adds a list of local decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the coupon
		 * controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/global
		 */

		return $this->_getSubManager( 'coupon', 'code/' . $manager, $name );
	}


	/**
	 * Get fields used for a search (allowed-/used keys / possibilities to use)
	 * Configuration of keys and types for frontend and backend usage.
	 *
	 * @see MShop_Common_Manager_Abstract Implemented methodes to use in search-methodes
	 * @see MW_Common_Manager_Abstract Implemented methodes uses this array
	 *
	 * @param boolean $withsub Not implemented/ needed yet.
	 * @return array Returns a list which implements MW_Common_Criteria_Attribute_Interface' items
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();
		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$context = $this->_getContext();
			$subcfg = 'classes/coupon/manager/code/submanagers';
			$submanager = $context->getConfig()->get($subcfg, array( ) );
			foreach($submanager  as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}

			$refcfg = 'classes/coupon/manager/code/refmanagers';
			$refmanager = $context->getConfig()->get($refcfg, array() );
			foreach( $refmanager as $domain ) {
				$list = array_merge( $list, $this->_createDomainManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Creates a new empty coupon code instance
	 *
	 * @return MShop_Coupon_Item_Code_Interface Emtpy coupon code object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns the coupon code object specified by its ID.
	 *
	 * @param integer $id Unique ID of the coupon code in the storage
	 * @return MShop_Coupon_Item_Code_Interface Coupon code object
	 * @throws MShop_Coupon_Exception If coupon couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'coupon.code.id', $id, $ref );
	}


	/**
	 * Saves a modified code object to the storage.
	 *
	 * @param MShop_Coupon_Item_Code_Interface $code Coupon code object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Coupon_Exception If coupon couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Interface $code, $fetch = true )
	{
		$iface = 'MShop_Coupon_Item_Code_Interface';
		if( !( $code instanceof $iface ) ) {
			throw new MShop_Coupon_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$code->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $code->getId();

			$path = 'mshop/coupon/manager/code/default/item/';
			$path .= ( $id === null ? 'insert' : 'update' );

			$stmt = $this->_getCachedStatement($conn, $path);

			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $code->getCouponId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $code->getCode() );
			$stmt->bind( 4, $code->getCount(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $code->getDateStart(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 6, $code->getDateEnd(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 7, date( 'Y-m-d H:i:s' ) );// mtime
			$stmt->bind( 8, $context->getEditor() );

			if( $id !== null) {
				$stmt->bind( 9, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 9, date( 'Y-m-d H:i:s' ) );// ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/coupon/manager/code/default/item/newid';
					$code->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$code->setId( $id );
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
		$path = 'mshop/coupon/manager/code/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * Possible search keys: 'coupon.code.id', 'coupon.code.couponid',
	 * 'coupon.code.code', 'coupon.code.count'.
	 *
	 * @param integer &$total Number of items that are available in total (not yet implemented)
	 * @return array List of code items implementing MShop_Coupon_Item_Code_Interface's
	 * @throws MShop_Coupon_Exception
	 * @throws MW_Common_Exception
	 * @throws MW_DB_Exception
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
			$cfgPathSearch = 'mshop/coupon/manager/code/default/item/search';
			$cfgPathCount =  'mshop/coupon/manager/code/default/item/count';
			$required = array( 'coupon.code' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
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
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be decreased
	 */
	public function decrease( $couponCode, $amount )
	{
		$this->increase( $couponCode, -$amount );
	}



	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be increased
	 */
	public function increase( $couponCode, $amount )
	{
		$context = $this->_getContext();

		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.siteid', $context->getLocale()->getSitePath() ) );

		$types = array(	'coupon.code.siteid' => $this->_searchConfig['coupon.code.siteid']['internaltype'] );
		$translations = array( 'coupon.code.siteid' => 'siteid' );
		$conditions = $search->getConditionString( $types, $translations );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$path = 'mshop/coupon/manager/code/default/item/counter';
			$stmt = $conn->create( str_replace( ':cond', $conditions, $context->getConfig()->get( $path, $path ) ) );

			$stmt->bind( 1, $amount, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) );// mtime
			$stmt->bind( 3, $context->getEditor() );
			$stmt->bind( 4, $couponCode);

			$result = $stmt->execute()->finish();
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$dbm->release( $conn, $dbname );
	}


	/**
	 * Creates a new code instance
	 *
	 * @return MShop_Coupon_Item_Code_Interface Emtpy coupon code object
	 */
	public function _createItem(array $values = array() )
	{
		return new MShop_Coupon_Item_Code_Default( $values );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$object = new MW_Common_Criteria_SQL( $conn );

		$dbm->release( $conn, $dbname );

		if( $default === true )
		{
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr = array();

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.code.datestart', null );
			$temp[] = $object->compare( '<=', 'coupon.code.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.code.dateend', null );
			$temp[] = $object->compare( '>=', 'coupon.code.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );
		}

		return $object;
	}
}