<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for managing property items
 *
 * @package MShop
 * @subpackage Common
 */
class Property
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
	implements \Aimeos\MShop\Common\Manager\PropertyRef\Iface
{
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;

	private string $domain;


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$domain = $this->domain();
		$alias = $this->alias( $domain . '.property.id' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $this->context()->config()->get( 'mshop/' . $domain . '/manager/sitemode', $level );

		return $this->getManager()->getSearchAttributes( $withsub ) + $this->createAttributes( [
			$domain . ':prop' => [
				'code' => $domain . ':prop()',
				'internalcode' => ':site AND :key AND ' . $alias . '."id"',
				'internaldeps' => [
					'LEFT JOIN "mshop_' . $domain . '_property" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'
				],
				'label' => 'Has property item, parameter(<property type>[,<language code>[,<property value>]])',
				'type' => 'null',
				'public' => false,
				'function' => function( &$source, array $params ) use ( $alias, $level ) {
					$keys = [];
					$langs = array_key_exists( 1, $params ) ? ( $params[1] ?? 'null' ) : '';

					foreach( (array) $langs as $lang ) {
						foreach( (array) ( $params[2] ?? '' ) as $val ) {
							$keys[] = substr( $params[0] . '|' . ( $lang === null ? 'null|' : ( $lang ? $lang . '|' : '' ) ) . $val, 0, 255 );
						}
					}

					$sitestr = $this->siteString( $alias . '."siteid"', $level );
					$keystr = $this->toExpression( $alias . '."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
					$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

					return $params;
				}
			],
		] );
	}


	/**
	 * Saves the dependent items of the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	public function saveRefs( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		$this->savePropertyItems( $item, $this->domain(), $fetch );

		return $this->getManager()->saveRefs( $item );
	}


	/**
	 * Merges the data from the given map and the referenced items
	 *
	 * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
	 * @param array $ref List of referenced items to fetch and add to the entries
	 * @return array Associative list of ID as key and the updated entries as value
	 */
	public function searchRefs( array $entries, array $ref ) : array
	{
		$entries = $this->getManager()->searchRefs( $entries, $ref );
		$domain = $this->domain();

		if( $this->hasRef( $ref, $domain . '/property' ) )
		{
			foreach( $this->getPropertyItems( array_keys( $entries ), $domain, $ref ) as $id => $list ) {
				$entries[$id]['.propitems'] = $list;
			}
		}

		return $entries;
	}


	/**
	 * Returns the domain of the manager
	 *
	 * @return string Domain of the manager
	 */
	protected function domain() : string
	{
		if( !isset( $this->domain ) ) {
			$this->domain = current( $this->getManager()->type() ) ?: '';
		}

		return $this->domain;
	}
}
