<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
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
		'index.text.name' => array(
			'code' => 'index.text.name()',
			'internalcode' => '( SELECT mindte_name."prodid"
				FROM "mshop_index_text" AS mindte_name
				WHERE :site AND mpro."id" = mindte_name."prodid"
				AND mindte_name."type" = \'name\' AND mindte_name."domain" = \'product\'
				AND ( mindte_name."langid" = $1 OR mindte_name."langid" IS NULL )
				AND mindte_name."value" @@ to_tsquery( $2 ) )',
			'label' => 'Product name, parameter(<language ID>,<text>)',
			'type' => 'null',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_NULL,
			'public' => false,
		),
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
		// @deprecated Removed 2019.01, Results are ordered by default
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

		$func = function( array $params, $pos ) {

			if( isset( $params[$pos] ) )
			{
				$regex = '/(\s|\&|\%|\?|\#|\=|\{|\}|\||\\\\|\~|\[|\]|\`|\^|\/|\-|\+|\>|\<|\(|\)|\*|\:|\"|\!|\§|\$|\'|\;|\.|\,|\@)+/';
				$search = trim( preg_replace( $regex, ' ', $params[$pos] ) );

				$params[$pos] = '\'' . implode( ':* & ', explode( ' ', $search ) ) . ':*\'';
			}

			return $params;
		};

		$this->searchConfig['index.text.name']['function'] = function( array $params ) use ( $func ) { return $func( $params, 1 ); };
		$this->searchConfig['index.text.relevance']['function'] = function( array $params ) use ( $func ) { return $func( $params, 2 ); };
		$this->searchConfig['sort:index.text.relevance']['function'] = function( array $params ) use ( $func ) { return $func( $params, 2 ); };

		$this->replaceSiteMarker( $this->searchConfig['index.text.name'], 'mindte_name."siteid"', $site );
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
}