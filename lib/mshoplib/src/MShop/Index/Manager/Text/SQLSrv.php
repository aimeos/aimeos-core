<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * SQL Server based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class SQLSrv
	extends \Aimeos\MShop\Index\Manager\Text\Standard
{
	private $searchConfig = array(
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
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

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/index/manager/sitemode', $level );

		if( $context->getConfig()->get( 'mshop/index/manager/text/sqlsrv/fulltext', false ) )
		{
			$search = ':site AND mindte."langid" = $1 AND (
				SELECT mindte_ft.RANK
				FROM CONTAINSTABLE("mshop_index_text", "content", $2) AS mindte_ft
				WHERE mindte."id" = mindte_ft."KEY"
			)';
			$sort = 'mindte_ft.RANK';

			$func = $this->getFunctionRelevance();
		}
		else
		{
			$search = ':site AND mindte."langid" = $1 AND CHARINDEX( $2, content )';
			$sort = '-CHARINDEX( $2, content )';

			$func = function( $source, array $params ) {

				if( isset( $params[1] ) ) {
					$params[1] = mb_strtolower( $params[1] );
				}

				return $params;
			};
		}

		$expr = $this->getSiteString( 'mindte."siteid"', $level );

		$this->searchConfig['index.text:relevance']['internalcode'] = str_replace( ':site', $expr, $search );
		$this->searchConfig['sort:index.text:relevance']['internalcode'] = $sort;
		$this->searchConfig['index.text:relevance']['function'] = $func;
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
						$strings[] = '"' . $part . '*"';
					}
				}

				$params[1] = '\'' . join( ' | ', $strings ) . '\'';
			}

			return $params;
		};
	}
}
