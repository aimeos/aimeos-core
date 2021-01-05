<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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
			'internalcode' => ':site AND mindte."langid" = $1 AND CAST( to_tsvector(mindte."content") @@ to_tsquery( $2 ) AS integer )',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
			'internalcode' => 'ts_rank(to_tsvector(mindte."content"), to_tsquery( $2 ))',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
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

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/index/manager/sitemode', $level );

		$name = 'index.text:relevance';
		$expr = $this->getSiteString( 'mindte."siteid"', $level );
		$this->searchConfig[$name]['internalcode'] = str_replace( ':site', $expr, $this->searchConfig[$name]['internalcode'] );
		$this->searchConfig[$name]['function'] = $this->searchConfig['sort:' . $name]['function'] = $this->getFunctionRelevance();
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attriubte items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}


	/**
	 * Returns the search function for searching by relevance
	 *
	 * @return \Closure Relevance search function
	 */
	protected function getFunctionRelevance()
	{
		return function( $source, array $params ) {

			if( isset( $params[1] ) )
			{
				$strings = [];
				$regex = '/(\&|\||\!|\-|\+|\>|\<|\(|\)|\~|\*|\:|\"|\'|\@|\\| )+/';
				$search = trim( mb_strtolower( preg_replace( $regex, ' ', $params[1] ) ), "' \t\n\r\0\x0B" );

				foreach( explode( ' ', $search ) as $part )
				{
					if( strlen( $part ) > 2 ) {
						$strings[] = $part . ':*';
					}
				}

				$params[1] = join( ' | ', $strings ) . ' | "' . join( ' <-> ', explode( ' ', $search ) ) . '"';
			}

			return $params;
		};
	}
}
