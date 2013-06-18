<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
 */


/**
 * Default implementation of the customer class.
 *
 * @package MShop
 * @subpackage Customer
 */
class MShop_Customer_Manager_Default extends MShop_Customer_Manager_Abstract
{
	private $_salt;

	private $_searchConfig = array(
		'customer.id' => array(
			'label' => 'Customer ID',
			'code' => 'customer.id',
			'internalcode' => 'mcus."id"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.siteid' => array(
			'label' => 'Customer site ID',
			'code' => 'customer.siteid',
			'internalcode' => 'mcus."siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.label' => array(
			'label' => 'Customer label',
			'code' => 'customer.label',
			'internalcode' => 'mcus."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.code' => array(
			'label' => 'Customer code',
			'code' => 'customer.code',
			'internalcode' => 'mcus."code"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.salutation' => array(
			'label' => 'Customer salutation',
			'code' => 'customer.salutation',
			'internalcode' => 'mcus."salutation"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.company'=> array(
			'label' => 'Customer company',
			'code' => 'customer.company',
			'internalcode' => 'mcus."company"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.title' => array(
			'label' => 'Customer title',
			'code' => 'customer.title',
			'internalcode' => 'mcus."title"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.firstname' => array(
			'label' => 'Customer firstname',
			'code' => 'customer.firstname',
			'internalcode' => 'mcus."firstname"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.lastname' => array(
			'label' => 'Customer lastname',
			'code' => 'customer.lastname',
			'internalcode' => 'mcus."lastname"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address1' => array(
			'label' => 'Customer address part one',
			'code' => 'customer.address1',
			'internalcode' => 'mcus."address1"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address2' => array(
			'label' => 'Customer address part two',
			'code' => 'customer.address2',
			'internalcode' => 'mcus."address2"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address3' => array(
			'label' => 'Customer address part three',
			'code' => 'customer.address3',
			'internalcode' => 'mcus."address3"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.postal' => array(
			'label' => 'Customer postal',
			'code' => 'customer.postal',
			'internalcode' => 'mcus."postal"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.city' => array(
			'label' => 'Customer city',
			'code' => 'customer.city',
			'internalcode' => 'mcus."city"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.state' => array(
			'label' => 'Customer state',
			'code' => 'customer.state',
			'internalcode' => 'mcus."state"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.languageid' => array(
			'label' => 'Customer language',
			'code' => 'customer.languageid',
			'internalcode' => 'mcus."langid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.countryid' => array(
			'label' => 'Customer country',
			'code' => 'customer.countryid',
			'internalcode' => 'mcus."countryid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.telephone' => array(
			'label' => 'Customer telephone',
			'code' => 'customer.telephone',
			'internalcode' => 'mcus."telephone"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.email' => array(
			'label' => 'Customer email',
			'code' => 'customer.email',
			'internalcode' => 'mcus."email"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.telefax' => array(
			'label' => 'Customer telefax',
			'code' => 'customer.telefax',
			'internalcode' => 'mcus."telefax"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.website' => array(
			'label' => 'Customer website',
			'code' => 'customer.website',
			'internalcode' => 'mcus."website"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.birthday' => array(
			'label' => 'Customer birthday',
			'code' => 'customer.birthday',
			'internalcode' => 'mcus."birthday"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.status'=> array(
			'label' => 'Customer status',
			'code' => 'customer.status',
			'internalcode' => 'mcus."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.password'=> array(
			'label' => 'Customer password',
			'code' => 'customer.password',
			'internalcode' => 'mcus."password"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.ctime'=> array(
			'label' => 'Customer creation time',
			'code' => 'customer.ctime',
			'internalcode' => 'mcus."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.mtime'=> array(
			'label' => 'Customer modification time',
			'code' => 'customer.mtime',
			'internalcode' => 'mcus."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.editor'=> array(
			'code'=>'customer.editor',
			'internalcode'=>'mcus."editor"',
			'label'=>'Customer editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_addressSearchConfig = array(
		'customer.address.id' => array(
			'label' => 'Customer address ID',
			'code' => 'customer.address.id',
			'internalcode' => 'mcusad."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_customer_address" AS mcusad ON ( mcus."id" = mcusad."refid" )' ),
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.address.siteid' => array(
			'label' => 'Customer address site ID',
			'code' => 'customer.address.siteid',
			'internalcode' => 'mcusad."siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.address.refid' => array(
			'label' => 'Customer address reference ID',
			'code' => 'customer.address.refid',
			'internalcode' => 'mcusad."refid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'customer.address.company'=> array(
			'label' => 'Customer address company',
			'code' => 'customer.address.company',
			'internalcode' => 'mcusad."company"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.salutation' => array(
			'label' => 'Customer address salutation',
			'code' => 'customer.address.salutation',
			'internalcode' => 'mcusad."salutation"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.title' => array(
			'label' => 'Customer address title',
			'code' => 'customer.address.title',
			'internalcode' => 'mcusad."title"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.firstname' => array(
			'label' => 'Customer address firstname',
			'code' => 'customer.address.firstname',
			'internalcode' => 'mcusad."firstname"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.lastname' => array(
			'label' => 'Customer address lastname',
			'code' => 'customer.address.lastname',
			'internalcode' => 'mcusad."lastname"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.address1' => array(
			'label' => 'Customer address address part one',
			'code' => 'customer.address.address1',
			'internalcode' => 'mcusad."address1"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.address2' => array(
			'label' => 'Customer address address part two',
			'code' => 'customer.address.address2',
			'internalcode' => 'mcusad."address2"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.address3' => array(
			'label' => 'Customer address address part three',
			'code' => 'customer.address.address3',
			'internalcode' => 'mcusad."address3"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.postal' => array(
			'label' => 'Customer address postal',
			'code' => 'customer.address.postal',
			'internalcode' => 'mcusad."postal"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.city' => array(
			'label' => 'Customer address city',
			'code' => 'customer.address.city',
			'internalcode' => 'mcusad."city"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.state' => array(
			'label' => 'Customer address state',
			'code' => 'customer.address.state',
			'internalcode' => 'mcusad."state"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.languageid' => array(
			'label' => 'Customer address language',
			'code' => 'customer.address.languageid',
			'internalcode' => 'mcusad."langid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.countryid' => array(
			'label' => 'Customer address country',
			'code' => 'customer.address.countryid',
			'internalcode' => 'mcusad."countryid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.telephone' => array(
			'label' => 'Customer address telephone',
			'code' => 'customer.address.telephone',
			'internalcode' => 'mcusad."telephone"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.email' => array(
			'label' => 'Customer address email',
			'code' => 'customer.address.email',
			'internalcode' => 'mcusad."email"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.telefax' => array(
			'label' => 'Customer address telefax',
			'code' => 'customer.address.telefax',
			'internalcode' => 'mcusad."telefax"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.website' => array(
			'label' => 'Customer address website',
			'code' => 'customer.address.website',
			'internalcode' => 'mcusad."website"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.flag' => array(
			'label' => 'Customer address flag',
			'code' => 'customer.address.flag',
			'internalcode' => 'mcusad."flag"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.address.position' => array(
			'code' => 'customer.address.position',
			'internalcode' => 'mcusad."pos"',
			'label' => 'Customer address position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.address.ctime'=> array(
			'code'=>'customer.address.ctime',
			'internalcode'=>'mcusad."ctime"',
			'label'=>'Customer address create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.mtime'=> array(
			'code'=>'customer.address.mtime',
			'internalcode'=>'mcusad."mtime"',
			'label'=>'Customer address modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.address.editor'=> array(
			'code'=>'customer.address.editor',
			'internalcode'=>'mcusad."editor"',
			'label'=>'Customer address editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listSearchConfig = array(
		'customer.list.id'=> array(
			'code'=>'customer.list.id',
			'internalcode'=>'mcusli."id"',
			'label'=>'Customer list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.siteid'=> array(
			'code'=>'customer.list.siteid',
			'internalcode'=>'mcusli."siteid"',
			'label'=>'Customer list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.parentid'=> array(
			'code'=>'customer.list.parentid',
			'internalcode'=>'mcusli."parentid"',
			'label'=>'Customer list parent Id',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.domain'=> array(
			'code'=>'customer.list.domain',
			'internalcode'=>'mcusli."domain"',
			'label'=>'Customer list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.typeid'=> array(
			'code'=>'customer.list.typeid',
			'internalcode'=>'mcusli."typeid"',
			'label'=>'Customer list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.refid'=> array(
			'code'=>'customer.list.refid',
			'internalcode'=>'mcusli."refid"',
			'label'=>'Customer list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.datestart' => array(
			'code'=>'customer.list.datestart',
			'internalcode'=>'mcusli."start"',
			'label'=>'Customer list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.dateend' => array(
			'code'=>'customer.list.dateend',
			'internalcode'=>'mcusli."end"',
			'label'=>'Customer list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.position' => array(
			'code'=>'customer.list.position',
			'internalcode'=>'mcusli."pos"',
			'label'=>'Customer list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.list.ctime'=> array(
			'code'=>'customer.list.ctime',
			'internalcode'=>'mcusli."ctime"',
			'label'=>'Customer list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.list.mtime'=> array(
			'code'=>'customer.list.mtime',
			'internalcode'=>'mcusli."mtime"',
			'label'=>'Customer list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.list.editor'=> array(
			'code'=>'customer.list.editor',
			'internalcode'=>'mcusli."editor"',
			'label'=>'Customer list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
	);


	private $_listTypeSearchConfig = array(
		'customer.list.type.id' => array(
			'code'=>'customer.list.type.id',
			'internalcode'=>'mcuslity."id"',
			'internaldeps'=>array('LEFT JOIN "mshop_customer_list_type" AS mcuslity ON ( mcusli."typeid" = mcuslity."id" )'),
			'label'=>'Customer list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.type.siteid' => array(
			'code'=>'customer.list.type.siteid',
			'internalcode'=>'mcuslity."siteid"',
			'label'=>'Customer list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.type.code' => array(
			'code'=>'customer.list.type.code',
			'internalcode'=>'mcuslity."code"',
			'label'=>'Customer list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.type.domain' => array(
			'code'=>'customer.list.type.domain',
			'internalcode'=>'mcuslity."domain"',
			'label'=>'Customer list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.type.label' => array(
			'code'=>'customer.list.type.label',
			'internalcode'=>'mcuslity."label"',
			'label'=>'Customer list type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.type.status' => array(
			'code'=>'customer.list.type.status',
			'internalcode'=>'mcuslity."status"',
			'label'=>'Customer list type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.list.type.ctime'=> array(
			'code'=>'customer.list.type.ctime',
			'internalcode'=>'mcuslity."ctime"',
			'label'=>'Customer list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.list.type.mtime'=> array(
			'code'=>'customer.list.type.mtime',
			'internalcode'=>'mcuslity."mtime"',
			'label'=>'Customer list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.list.type.editor'=> array(
			'code'=>'customer.list.type.editor',
			'internalcode'=>'mcuslity."editor"',
			'label'=>'Customer list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Initializes a new customer manager object using the given context object.
	 *
	 * @param MShop_Context_Interface $_context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_salt = $context->getConfig()->get( 'mshop/customer/manager/default/salt/', 'mshop' );
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
			$config = $this->_getContext()->getConfig();

			foreach( $config->get( 'classes/customer/manager/submanagers', array( 'address', 'list' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Instantiates a new customer item object.
	 *
	 * @return MShop_Customer_Item_Interface
	 */
	public function createItem()
	{
		$values = array( 'siteid'=> $this->_getContext()->getLocale()->getSiteId() );

		$addressManager = $this->getSubManager( 'address' );
		$address = $addressManager->createItem();

		return $this->_createItem( $address, $values );
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param MShop_Common_Item_Address_Interface $address billingaddress of customer item
	 * @param array $values List of attributes for customer item
	 * @param string $salt Salt for password encryption that will be concatenated at the end of customer password
	 * @return MShop_Customer_Item_Interface New customer item
	 */
	protected function _createItem( MShop_Common_Item_Address_Interface $address, array $values = array(),
		array $listItems = array(), array $refItems = array() )
	{
		return new MShop_Customer_Item_Default( $address, $values, $listItems, $refItems, $this->_salt );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/customer/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Saves a customer item object.
	 *
	 * @param MShop_Customer_Item_Interface $item Customer item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Customer_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Customer_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/customer/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$sql = $config->get( $path, $path );

			$stmt = $conn->create( $sql );
			$billingAddress = $item->getBillingAddress();

			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getLabel() );
			$stmt->bind( 3, $item->getCode() );
			$stmt->bind( 4, $billingAddress->getCompany(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 5, $billingAddress->getSalutation(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 6, $billingAddress->getTitle(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 7, $billingAddress->getFirstname(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 8, $billingAddress->getLastname(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 9, $billingAddress->getAddress1(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 10, $billingAddress->getAddress2(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 11, $billingAddress->getAddress3(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 12, $billingAddress->getPostal(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 13, $billingAddress->getCity(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 14, $billingAddress->getState(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 15, $billingAddress->getCountryId(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 16, $billingAddress->getLanguageId(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 17, $billingAddress->getTelephone(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 18, $billingAddress->getEmail(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 19, $billingAddress->getTelefax(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 20, $billingAddress->getWebsite(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 21, $item->getBirthday(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 22, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 23, $item->getPassword() );
			$stmt->bind( 24, date( 'Y-m-d H:i:s', time() ) ); // Modification time
			$stmt->bind( 25, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 26, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 26, date( 'Y-m-d H:i:s', time() ) ); // Creation time
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/customer/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Customer_Item_Interface
	 * @throws MShop_Customer_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();
		$map = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/customer/manager/default/item/search';
			$cfgPathCount = 'mshop/customer/manager/default/item/count';
			$required = array( 'customer' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$map[ $row['id'] ] = $row;
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $this->_buildItems( $map, $ref, 'customer' );
	}


	/**
	 * Returns a new manager for customer extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'address':
				$path = 'mshop/customer/manager/address/default/item';
				return $this->_createAddressManager( 'customer', $name, $path, $this->_addressSearchConfig );
			case 'list':
				$typeManager = $this->_getTypeManager( 'customer', 'list/type', null, $this->_listTypeSearchConfig );
				return $this->_getListManager( 'customer', $manager, $name, $this->_listSearchConfig, $typeManager );

			default:
				return $this->_getSubManager( 'customer', $manager, $name );
		}
	}


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param array $domains List of domain names whose referenced items should be attached
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	protected function _buildItems( array $map, array $domains, $prefix )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = array();

		if( !empty( $domains ) )
		{
			$listItems = $this->_getListItems( array_keys( $map ), $domains, $prefix );

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[ $parentid ][ $domain ][ $listItem->getId() ] = $listItem;
				$refIdMap[ $domain ][ $listItem->getRefId() ][] = $parentid;
			}

			$refItemMap = $this->_getRefItems( $refIdMap );
		}

		$addressManager = $this->getSubManager( 'address' );

		foreach ( $map as $id => $values )
		{
			$listItems = array();
			if ( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = array();
			if ( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			// Hand over empty address item, which will be filled in the customer item constructor
			$items[ $id ] = $this->_createItem( $addressManager->createItem(), $values, $listItems, $refItems );
		}

		return $items;
	}
}
