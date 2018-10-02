<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager;


/**
 * Default implementation of the customer class.
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Customer\Manager\Base
	implements \Aimeos\MShop\Customer\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	use \Aimeos\MShop\Common\Manager\AddressRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	private $searchConfig = array(
		// no siteid
		'customer.id' => array(
			'label' => 'ID',
			'code' => 'customer.id',
			'internalcode' => 'mcus."id"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'customer.code' => array(
			'label' => 'Username',
			'code' => 'customer.code',
			'internalcode' => 'mcus."code"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.label' => array(
			'label' => 'Label',
			'code' => 'customer.label',
			'internalcode' => 'mcus."label"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.salutation' => array(
			'label' => 'Salutation',
			'code' => 'customer.salutation',
			'internalcode' => 'mcus."salutation"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.company' => array(
			'label' => 'Company',
			'code' => 'customer.company',
			'internalcode' => 'mcus."company"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.vatid' => array(
			'label' => 'Vat ID',
			'code' => 'customer.vatid',
			'internalcode' => 'mcus."vatid"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.title' => array(
			'label' => 'Title',
			'code' => 'customer.title',
			'internalcode' => 'mcus."title"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.firstname' => array(
			'label' => 'Firstname',
			'code' => 'customer.firstname',
			'internalcode' => 'mcus."firstname"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.lastname' => array(
			'label' => 'Lastname',
			'code' => 'customer.lastname',
			'internalcode' => 'mcus."lastname"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.address1' => array(
			'label' => 'Address part one',
			'code' => 'customer.address1',
			'internalcode' => 'mcus."address1"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.address2' => array(
			'label' => 'Address part two',
			'code' => 'customer.address2',
			'internalcode' => 'mcus."address2"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.address3' => array(
			'label' => 'Address part three',
			'code' => 'customer.address3',
			'internalcode' => 'mcus."address3"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.postal' => array(
			'label' => 'Postal',
			'code' => 'customer.postal',
			'internalcode' => 'mcus."postal"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.city' => array(
			'label' => 'City',
			'code' => 'customer.city',
			'internalcode' => 'mcus."city"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.state' => array(
			'label' => 'State',
			'code' => 'customer.state',
			'internalcode' => 'mcus."state"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.languageid' => array(
			'label' => 'Language',
			'code' => 'customer.languageid',
			'internalcode' => 'mcus."langid"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.countryid' => array(
			'label' => 'Country',
			'code' => 'customer.countryid',
			'internalcode' => 'mcus."countryid"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.telephone' => array(
			'label' => 'Telephone',
			'code' => 'customer.telephone',
			'internalcode' => 'mcus."telephone"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.email' => array(
			'label' => 'E-mail',
			'code' => 'customer.email',
			'internalcode' => 'mcus."email"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.telefax' => array(
			'label' => 'Facsimile',
			'code' => 'customer.telefax',
			'internalcode' => 'mcus."telefax"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.website' => array(
			'label' => 'Web site',
			'code' => 'customer.website',
			'internalcode' => 'mcus."website"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.longitude' => array(
			'label' => 'Longitude',
			'code' => 'customer.longitude',
			'internalcode' => 'mcus."longitude"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'customer.latitude' => array(
			'label' => 'Latitude',
			'code' => 'customer.latitude',
			'internalcode' => 'mcus."latitude"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'customer.birthday' => array(
			'label' => 'Birthday',
			'code' => 'customer.birthday',
			'internalcode' => 'mcus."birthday"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.status' => array(
			'label' => 'Status',
			'code' => 'customer.status',
			'internalcode' => 'mcus."status"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'customer.dateverified' => array(
			'label' => 'Verification date',
			'code' => 'customer.dateverified',
			'internalcode' => 'mcus."vdate"',
			'type' => 'date',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'customer.password' => array(
			'label' => 'Password',
			'code' => 'customer.password',
			'internalcode' => 'mcus."password"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'customer.ctime' => array(
			'label' => 'Create date/time',
			'code' => 'customer.ctime',
			'internalcode' => 'mcus."ctime"',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'customer.mtime' => array(
			'label' => 'Modify date/time',
			'code' => 'customer.mtime',
			'internalcode' => 'mcus."mtime"',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'customer.editor' => array(
			'label' => 'Editor',
			'code' => 'customer.editor',
			'internalcode' => 'mcus."editor"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
	);


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/customer/manager/submanagers';
		$default = ['address', 'group', 'lists', 'property'];

		foreach( $this->getContext()->getConfig()->get( $path, $default ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/customer/manager/standard/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param string|null Type the item should be created with
	 * @param string|null Domain of the type the item should be created with
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Customer\Item\Iface New customer item object
	 */
	public function createItem( $type = null, $domain = null, array $values = [] )
	{
		$values['customer.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/customer/manager/submanagers';
		$default = ['address', 'group', 'lists', 'property'];

		return $this->getResourceTypeBase( 'customer', $path, $default, $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/customer/manager/submanagers
		 * List of manager names that can be instantiated by the customer manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'mshop/customer/manager/submanagers';
		$default = ['address', 'group', 'lists', 'property'];

		return $this->getSearchAttributesBase( $this->searchConfig, $path, $default, $withsub );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/customer/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/customer/manager/standard/delete/ansi
		 */

		/** mshop/customer/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the customer database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/customer/manager/standard/insert/ansi
		 * @see mshop/customer/manager/standard/update/ansi
		 * @see mshop/customer/manager/standard/newid/ansi
		 * @see mshop/customer/manager/standard/search/ansi
		 * @see mshop/customer/manager/standard/count/ansi
		 */
		$path = 'mshop/customer/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Saves a customer item object.
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		self::checkClass( '\\Aimeos\\MShop\\Customer\\Item\\Iface', $item );

		if( !$item->isModified() )
		{
			$item = $this->savePropertyItems( $item, 'customer', $fetch );
			$item = $this->saveAddressItems( $item, 'customer', $fetch );
			return $this->saveListItems( $item, 'customer', $fetch );
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$billingAddress = $item->getPaymentAddress();

			if( $id === null )
			{
				/** mshop/customer/manager/standard/insert/mysql
				 * Inserts a new customer record into the database table
				 *
				 * @see mshop/customer/manager/standard/insert/ansi
				 */

				/** mshop/customer/manager/standard/insert/ansi
				 * Inserts a new customer record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the customer item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/customer/manager/standard/update/ansi
				 * @see mshop/customer/manager/standard/newid/ansi
				 * @see mshop/customer/manager/standard/delete/ansi
				 * @see mshop/customer/manager/standard/search/ansi
				 * @see mshop/customer/manager/standard/count/ansi
				 */
				$path = 'mshop/customer/manager/standard/insert';
			}
			else
			{
				/** mshop/customer/manager/standard/update/mysql
				 * Updates an existing customer record in the database
				 *
				 * @see mshop/customer/manager/standard/update/ansi
				 */

				/** mshop/customer/manager/standard/update/ansi
				 * Updates an existing customer record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the customer item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/customer/manager/standard/insert/ansi
				 * @see mshop/customer/manager/standard/newid/ansi
				 * @see mshop/customer/manager/standard/delete/ansi
				 * @see mshop/customer/manager/standard/search/ansi
				 * @see mshop/customer/manager/standard/count/ansi
				 */
				$path = 'mshop/customer/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getLabel() );
			$stmt->bind( 3, $item->getCode() );
			$stmt->bind( 4, $billingAddress->getCompany() );
			$stmt->bind( 5, $billingAddress->getVatID() );
			$stmt->bind( 6, $billingAddress->getSalutation() );
			$stmt->bind( 7, $billingAddress->getTitle() );
			$stmt->bind( 8, $billingAddress->getFirstname() );
			$stmt->bind( 9, $billingAddress->getLastname() );
			$stmt->bind( 10, $billingAddress->getAddress1() );
			$stmt->bind( 11, $billingAddress->getAddress2() );
			$stmt->bind( 12, $billingAddress->getAddress3() );
			$stmt->bind( 13, $billingAddress->getPostal() );
			$stmt->bind( 14, $billingAddress->getCity() );
			$stmt->bind( 15, $billingAddress->getState() );
			$stmt->bind( 16, $billingAddress->getCountryId() );
			$stmt->bind( 17, $billingAddress->getLanguageId() );
			$stmt->bind( 18, $billingAddress->getTelephone() );
			$stmt->bind( 19, $billingAddress->getEmail() );
			$stmt->bind( 20, $billingAddress->getTelefax() );
			$stmt->bind( 21, $billingAddress->getWebsite() );
			$stmt->bind( 22, $billingAddress->getLongitude() );
			$stmt->bind( 23, $billingAddress->getLatitude() );
			$stmt->bind( 24, $item->getBirthday() );
			$stmt->bind( 25, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 26, $item->getDateVerified() );
			$stmt->bind( 27, $item->getPassword() );
			$stmt->bind( 28, $date ); // Modification time
			$stmt->bind( 29, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 30, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$billingAddress->setId( $id ); // enforce ID to be present
				$item->setId( $id );
			} else {
				$stmt->bind( 30, $date ); // Creation time
			}

			$stmt->execute()->finish();

			if( $id === null )
			{
				/** mshop/customer/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/customer/manager/standard/newid/ansi
				 */

				/** mshop/customer/manager/standard/newid/ansi
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * As soon as a new record is inserted into the database table,
				 * the database server generates a new and unique identifier for
				 * that record. This ID can be used for retrieving, updating and
				 * deleting that specific record from the table again.
				 *
				 * For MySQL:
				 *  SELECT LAST_INSERT_ID()
				 * For PostgreSQL:
				 *  SELECT currval('seq_mcus_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mcus_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/customer/manager/standard/insert/ansi
				 * @see mshop/customer/manager/standard/update/ansi
				 * @see mshop/customer/manager/standard/delete/ansi
				 * @see mshop/customer/manager/standard/search/ansi
				 * @see mshop/customer/manager/standard/count/ansi
				 */
				$path = 'mshop/customer/manager/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$this->addGroups( $item );

		$item = $this->savePropertyItems( $item, 'customer', $fetch );
		$item = $this->saveAddressItems( $item, 'customer', $fetch );
		return $this->saveListItems( $item, 'customer', $fetch );
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array Associative list of IDs as keys and items implementing \Aimeos\MShop\Customer\Item\Iface as values
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'customer' );

			/** mshop/customer/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole customer domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/standard/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param integer Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2018.01
			 * @see mshop/locale/manager/standard/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/customer/manager/sitemode', $level );

			/** mshop/customer/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/customer/manager/standard/search/ansi
			 */

			/** mshop/customer/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the customer
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * If the records that are retrieved should be ordered by one or more
			 * columns, the generated string of column / sort direction pairs
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for ordering the result set but then
			 * no index can be used.
			 *
			 * The number of returned records can be limited and can start at any
			 * number between the begining and the end of the result set. For that
			 * the ":size" and ":start" placeholders are replaced by the
			 * corresponding values from the criteria object. The default values
			 * are 0 for the start and 100 for the size value.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for searching items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/customer/manager/standard/insert/ansi
			 * @see mshop/customer/manager/standard/update/ansi
			 * @see mshop/customer/manager/standard/newid/ansi
			 * @see mshop/customer/manager/standard/delete/ansi
			 * @see mshop/customer/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/customer/manager/standard/search';

			/** mshop/customer/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/customer/manager/standard/count/ansi
			 */

			/** mshop/customer/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the customer
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
			 * current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * Both, the strings for ":joins" and for ":cond" are the same as for
			 * the "search" SQL statement.
			 *
			 * Contrary to the "search" statement, it doesn't return any records
			 * but instead the number of records that have been found. As counting
			 * thousands of records can be a long running task, the maximum number
			 * of counted records is limited for performance reasons.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for counting items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/customer/manager/standard/insert/ansi
			 * @see mshop/customer/manager/standard/update/ansi
			 * @see mshop/customer/manager/standard/newid/ansi
			 * @see mshop/customer/manager/standard/delete/ansi
			 * @see mshop/customer/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/customer/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$map[$row['customer.id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$addrItems = [];
		if( in_array( 'customer/address', $ref, true ) ) {
			$addrItems = $this->getAddressItems( array_keys( $map ), 'customer' );
		}

		$propItems = [];
		if( in_array( 'customer/property', $ref, true ) ) {
			$propItems = $this->getPropertyItems( array_keys( $map ), 'customer' );
		}

		return $this->buildItems( $map, $ref, 'customer', $addrItems, $propItems );
	}


	/**
	 * Returns a new manager for customer extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'customer', $manager, $name );
	}
}
