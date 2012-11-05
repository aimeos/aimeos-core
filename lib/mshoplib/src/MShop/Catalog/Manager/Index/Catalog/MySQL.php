<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: MySQL.php 1334 2012-10-24 16:17:46Z doleiynyk $
 */


/**
 * MySQL based catalog index catalog for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Catalog_MySQL
	extends MShop_Catalog_Manager_Index_Catalog_Default
	implements MShop_Catalog_Manager_Index_Interface
{
	private $_searchConfig = array(
		'catalog.index.catalog.id' => array(
			'code'=>'catalog.index.catalog.id',
			'internalcode'=>':site AND mcatinca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_catalog" AS mcatinca USE INDEX ("idx_mscatinca_s_ca_lt_po", "idx_mscatinca_p_s_ca_lt_po") ON mcatinca."prodid" = mpro."id"' ),
			'label'=>'Product index category ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
	);


	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$site = $context->getLocale()->getSitePath();
		$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_INT );

		$search = $this->createSearch();
		$expr = array(
			$search->compare( '==', 'siteid', null ),
			$search->compare( '==', 'siteid', $site ),
		);
		$search->setConditions( $search->combine( '||', $expr ) );


		$string = $search->getConditionString( $types, array( 'siteid' => 'mcatinca."siteid"' ) );
		$this->_searchConfig['catalog.index.catalog.id']['internalcode'] =
			str_replace( ':site', $string, $this->_searchConfig['catalog.index.catalog.id']['internalcode'] );
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

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'mshop/catalog/manager/index/catalog/mysql/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
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
		$context = $this->_getContext();
		$config = $context->getConfig();
		$path = 'mshop/catalog/manager/index/catalog/mysql/cleanup';

		if( ( $sql = $config->get( $path, null ) ) === null ) {
			return;
		}

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}
}