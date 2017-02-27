<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * PostgreSQL based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class PgSQL
	extends \Aimeos\MShop\Index\Manager\Text\Standard
	implements \Aimeos\MShop\Index\Manager\Iface
{
	private $searchConfig = array(
		'index.text.relevance' => array(
			'code' => 'index.text.relevance()',
			'internalcode' => ':site AND mindte."listtype" IN ($1)
				AND ( mindte."langid" = $2 OR mindte."langid" IS NULL )
				AND CAST( mindte."value" @@ to_tsquery( $3 ) AS integer )',
			'label' => 'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:index.text.relevance' => array(
			'code' => 'sort:index.text.relevance()',
			'internalcode' => 'mindte."value" @@ to_tsquery($3)',
			'label' => 'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
	);


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$site = $context->getLocale()->getSitePath();

		$this->replaceSiteMarker( $this->searchConfig['index.text.relevance'], 'mindte."siteid"', $site );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$db = $this->getResourceName();

		$conn = $dbm->acquire( $db );
		$object = new \Aimeos\MW\Criteria\PgSQL( $conn );
		$dbm->release( $conn, $db );

		if( $default === true ) {
			$object->setConditions( parent::createSearch( $default )->getConditions() );
		}

		return $object;
	}
}