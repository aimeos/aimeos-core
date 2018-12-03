<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * MySQL based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Text\Standard
{
	private $searchConfig = array(
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
			'internalcode' => ':site AND mindte."listtype" = \'default\'
				AND ( mindte."langid" = $1 OR mindte."langid" IS NULL )
				AND MATCH( mindte."value" ) AGAINST( $2 IN BOOLEAN MODE )',
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
				$str = '';
				$list = ['-', '+', '>', '<', '(', ')', '~', '*', ':', '"', '&', '|', '!', '/', '§', '$', '%', '{', '}', '[', ']', '=', '?', '\\', '\'', '#', ';', '.', ',', '@'];
				$search = str_replace( $list, ' ', $params[1] );

				foreach( explode( ' ', $search ) as $part )
				{
					$len = strlen( $part );

					if( $len > 0 ) {
						$str .= ' +' . strtolower( $part ) . '*';
					}
				}

				$params[1] = '\'' . $str . '\'';
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