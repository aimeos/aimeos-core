<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Supplier
 */


/**
 * Implementation for supplier address manager.
 *
 * @package MShop
 * @subpackage Supplier
 */
class MShop_Supplier_Manager_Address_Default
	extends MShop_Common_Manager_Address_Abstract
	implements MShop_Supplier_Manager_Address_Interface
{
	private $_searchConfig = array(
		'supplier.address.id' => array(
			'code' => 'supplier.address.id',
			'internalcode' => 'msupad."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_supplier_address" AS msupad ON msupad."refid" = msup."id"' ),
			'label' => 'Supplier address ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'supplier.address.siteid' => array(
			'code' => 'supplier.address.siteid',
			'internalcode' => 'msupad."siteid"',
			'label' => 'Supplier address site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'supplier.address.refid' => array(
			'code' => 'supplier.address.refid',
			'internalcode' => 'msupad."refid"',
			'label' => 'Supplier address reference ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'supplier.address.company'=> array(
			'code' => 'supplier.address.company',
			'internalcode' => 'msupad."company"',
			'label' => 'Supplier address company',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.salutation' => array(
			'code' => 'supplier.address.salutation',
			'internalcode' => 'msupad."salutation"',
			'label' => 'Supplier address salutation',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.title' => array(
			'code' => 'supplier.address.title',
			'internalcode' => 'msupad."title"',
			'label' => 'Supplier address title',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.firstname' => array(
			'code' => 'supplier.address.firstname',
			'internalcode' => 'msupad."firstname"',
			'label' => 'Supplier address firstname',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.lastname' => array(
			'code' => 'supplier.address.lastname',
			'internalcode' => 'msupad."lastname"',
			'label' => 'Supplier address lastname',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.address1' => array(
			'code' => 'supplier.address.address1',
			'internalcode' => 'msupad."address1"',
			'label' => 'Supplier address part one',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.address2' => array(
			'code' => 'supplier.address.address2',
			'internalcode' => 'msupad."address2"',
			'label' => 'Supplier address part two',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.address3' => array(
			'code' => 'supplier.address.address3',
			'internalcode' => 'msupad."address3"',
			'label' => 'Supplier address part three',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.postal' => array(
			'code' => 'supplier.address.postal',
			'internalcode' => 'msupad."postal"',
			'label' => 'Supplier address postal',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.city' => array(
			'code' => 'supplier.address.city',
			'internalcode' => 'msupad."city"',
			'label' => 'Supplier address city',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.state' => array(
			'code' => 'supplier.address.state',
			'internalcode' => 'msupad."state"',
			'label' => 'Supplier address state',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.countryid' => array(
			'code' => 'supplier.address.countryid',
			'internalcode' => 'msupad."countryid"',
			'label' => 'Supplier address country ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.languageid' => array(
			'code' => 'supplier.address.languageid',
			'internalcode' => 'msupad."langid"',
			'label' => 'Supplier address language ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.telephone' => array(
			'code' => 'supplier.address.telephone',
			'internalcode' => 'msupad."telephone"',
			'label' => 'Supplier address telephone',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.email' => array(
			'code' => 'supplier.address.email',
			'internalcode' => 'msupad."email"',
			'label' => 'Supplier address email',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.telefax' => array(
			'code' => 'supplier.address.telefax',
			'internalcode' => 'msupad."telefax"',
			'label' => 'Supplier address telefax',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.website' => array(
			'code' => 'supplier.address.website',
			'internalcode' => 'msupad."website"',
			'label' => 'Supplier address website',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.flag' => array(
			'code' => 'supplier.address.flag',
			'internalcode' => 'msupad."flag"',
			'label' => 'Supplier address flag',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.address.position' => array(
			'code' => 'supplier.address.position',
			'internalcode' => 'msupad."pos"',
			'label' => 'Supplier address position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.address.ctime'=> array(
			'code'=>'supplier.address.ctime',
			'internalcode'=>'msupad."ctime"',
			'label'=>'Supplier address create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.mtime'=> array(
			'code'=>'supplier.address.mtime',
			'internalcode'=>'msupad."mtime"',
			'label'=>'Supplier address modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.editor'=> array(
			'code'=>'supplier.address.editor',
			'internalcode'=>'msupad."editor"',
			'label'=>'Supplier address editor',
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
		$this->_setResourceName( 'db-supplier' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/supplier/manager/address/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/supplier/manager/address/default/item/delete' );
	}


	/**
	 * Returns the list attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes();

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/supplier/manager/address/submanagers';
			foreach( $context->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for address extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/supplier/manager/address/name
		 * Class name of the used supplier address manager implementation
		 *
		 * Each default supplier address manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Supplier_Manager_Address_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Supplier_Manager_Address_Myaddress
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/supplier/manager/address/name = Myaddress
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

		/** mshop/supplier/manager/address/decorators/excludes
		 * Excludes decorators added by the "common" option from the supplier address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the supplier address manager.
		 *
		 *  mshop/supplier/manager/address/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the address of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the supplier address manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/supplier/manager/address/decorators/global
		 * @see mshop/supplier/manager/address/decorators/local
		 */

		/** mshop/supplier/manager/address/decorators/global
		 * Adds a list of globally available decorators only to the supplier address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the supplier address manager.
		 *
		 *  mshop/supplier/manager/address/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the supplier controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/supplier/manager/address/decorators/excludes
		 * @see mshop/supplier/manager/address/decorators/local
		 */

		/** mshop/supplier/manager/address/decorators/local
		 * Adds a list of local decorators only to the supplier address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the supplier address manager.
		 *
		 *  mshop/supplier/manager/address/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the supplier
		 * controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/supplier/manager/address/decorators/excludes
		 * @see mshop/supplier/manager/address/decorators/global
		 */

		return $this->_getSubManager( 'supplier', 'address/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/supplier/manager/address/default/item';
	}


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}