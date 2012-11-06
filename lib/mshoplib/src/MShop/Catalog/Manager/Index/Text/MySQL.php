<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: MySQL.php 1360 2012-10-30 18:23:28Z doleiynyk $
 */


/**
 * MySQL based catalog index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Text_MySQL
	extends MShop_Catalog_Manager_Index_Text_Default
	implements MShop_Catalog_Manager_Index_Interface
{
	private $_searchConfig = array(
		'catalog.index.text.id' => array(
			'code'=>'catalog.index.text.id',
			'internalcode'=>':site AND mcatinte."textid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_text" AS mcatinte USE INDEX ("idx_mscatinte_value", "idx_mscatinte_p_s_lt_la_ty_va", "fk_mscatinte_textid") ON mcatinte."prodid" = mpro."id"' ),
			'label'=>'Product index text ID',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.text.relevance' => array(
			'code' => 'catalog.index.text.relevance()',
			'internalcode' => ':site AND mcatinte."listtype" = $1 AND mcatinte."langid" = $2 AND MATCH( mcatinte."value" ) AGAINST( $3 IN BOOLEAN MODE )',
			'label' => 'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
		'sort:catalog.index.text.relevance' => array(
			'code' => 'sort:catalog.index.text.relevance()',
			'internalcode' => 'MATCH( mcatinte."value" ) AGAINST( $3 IN BOOLEAN MODE )',
			'label' => 'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
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


		$string = $search->getConditionString( $types, array( 'siteid' => 'mcatinte."siteid"' ) );
		$this->_searchConfig['catalog.index.text.id']['internalcode'] =
			str_replace( ':site', $string, $this->_searchConfig['catalog.index.text.id']['internalcode'] );

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.text.relevance'], 'mcatinte."siteid"', $site );
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
		$path = 'mshop/catalog/manager/index/text/mysql/cleanup';

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