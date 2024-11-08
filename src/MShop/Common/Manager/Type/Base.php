<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Type;


/**
 * Abstract type manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Type\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Type\Iface New type item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$prefix = $this->prefix();
		$locale = $this->context()->locale();

		$values['.language'] = $locale->getLanguageId();
		$values[$prefix . 'siteid'] = $values[$prefix . 'siteid'] ?? $locale->getSiteId();

		return new \Aimeos\MShop\Common\Item\Type\Standard( $prefix, $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->filterBase( substr( $this->prefix(), 0, - 1 ), $default );
	}


	/**
	 * Returns the attributes that can be used for saving.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSaveAttributes( bool $withsub = true ) : array
	{
		$prefix = $this->prefix();

		return $this->createAttributes( [
			$prefix . 'label' => [
				'internalcode' => 'label',
				'label' => 'Type label',
			],
			$prefix . 'code' => [
				'internalcode' => 'code',
				'label' => 'Type code',
			],
			$prefix . 'domain' => [
				'internalcode' => 'domain',
				'label' => 'Type domain',
			],
			$prefix . 'position' => [
				'internalcode' => 'pos',
				'label' => 'Type position',
				'type' => 'int',
			],
			$prefix . 'status' => [
				'internalcode' => 'status',
				'label' => 'Type status',
				'type' => 'int',
			],
			$prefix . 'i18n' => [
				'internalcode' => 'i18n',
				'label' => 'Type localization',
				'type' => 'json',
				'public' => false,
			],
		] );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], ?string $domain = 'product', ?string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$prefix = $this->prefix();
		return $this->findBase( [$prefix . 'code' => $code, $prefix . 'domain' => $domain], $ref, $default );
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name e.g. "mshop_product_list_type"
	 */
	protected function table() : string
	{
		return str_replace( '_lists_', '_list_', parent::table() );
	}


	/**
	 * Returns the prefix used for the item keys.
	 *
	 * @return string Item key prefix
	 */
	protected function prefix() : string
	{
		return join( '.', $this->type() ) . '.';
	}
}
