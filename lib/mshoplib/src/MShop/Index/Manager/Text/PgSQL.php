<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
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
{
	private $searchConfig = array(
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
			'internalcode' => '(
				SELECT mindte2."prodid"
				FROM "mshop_index_text" AS mindte2
				WHERE :site AND mpro."id" = mindte2."prodid"
					AND ( mindte2."langid" = $1 OR mindte2."langid" IS NULL )
					AND mindte2."value" @@ to_tsquery( $2 )
				LIMIT 1
			)',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'null',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
			'internalcode' => '1',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
			'type' => 'null',
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

		$func = function( $source, array $params ) {

			if( isset( $params[1] ) )
			{
				$regex = '/(\s|\&|\%|\?|\#|\=|\{|\}|\||\\\\|\~|\[|\]|\`|\^|\/|\-|\+|\>|\<|\(|\)|\*|\:|\"|\!|\§|\$|\'|\;|\.|\,|\@)+/';
				$search = trim( preg_replace( $regex, ' ', $params[1] ) );

				$params[1] = '\'' . implode( ':* & ', explode( ' ', strtolower( $search ) ) ) . ':*\'';
			}

			return $params;
		};

		$this->searchConfig['index.text:relevance']['function'] = $func;
		$this->replaceSiteMarker( $this->searchConfig['index.text:relevance'], 'mindte."siteid"', $site );
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
}