<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: MySQL.php 14703 2012-01-05 09:57:40Z nsendetzky $
 */


/**
 * MySQL based catalog index for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_MySQL
	extends MShop_Catalog_Manager_Index_Default
	implements MShop_Catalog_Manager_Index_Interface
{
	private $_submanager = array();


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$default = array( 'price', 'catalog', 'attribute', 'text' );
		$subdomains = $context->getConfig()->get( 'mshop/catalog/manager/index/mysql/submanagers', $default );

		foreach( $subdomains as $domain ) {
			$this->_submanager[ $domain ] = $this->getSubManager( $domain, 'MySQL' );
		}

	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->_submanager as $submanager ) {
			$list = array_merge( $list, $submanager->getSearchAttributes() );
		}

		return $list;
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		foreach( $this->_submanager as $submanager ) {
			$submanager->optimize();
		}

		parent::optimize();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		$object = new MW_Common_Criteria_MySQL( $conn );

		$dbm->release( $conn );

		if( $default === true ) {
			$object->setConditions( parent::createSearch( $default )->getConditions() );
		}

		return $object;
	}
}