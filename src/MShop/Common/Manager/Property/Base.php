<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Property;


/**
 * Abstract property manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Property\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$prefix = $this->prefix();
		$locale = $this->context()->locale();

		$values['.languageid'] = $locale->getLanguageId();
		$values[$prefix . 'siteid'] = $values[$prefix . 'siteid'] ?? $locale->getSiteId();

		return new \Aimeos\MShop\Common\Item\Property\Standard( $prefix, $values );
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
		$filter = parent::filter();

		if( $default !== false )
		{
			$prefix = $this->prefix();
			$langid = $this->context()->locale()->getLanguageId();

			$filter->add( $filter->or( [
				$filter->compare( '==', $prefix . 'languageid', null ),
				$filter->compare( '==', $prefix . 'languageid', $langid ),
			] ) );
		}

		return $filter;
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
			$prefix . 'parentid' => [
				'internalcode' => 'parentid',
				'label' => 'Property parent ID',
				'type' => 'int',
				'public' => false,
			],
			$prefix . 'key' => [
				'internalcode' => 'key',
				'label' => 'Property key',
				'public' => false,
			],
			$prefix . 'type' => [
				'internalcode' => 'type',
				'label' => 'Property type',
			],
			$prefix . 'value' => [
				'internalcode' => 'value',
				'label' => 'Property value',
			],
			$prefix . 'languageid' => [
				'internalcode' => 'langid',
				'label' => 'Property language ID',
			],
		] );
	}


	/**
	 * Returns the domain prefix.
	 *
	 * @return string Domain prefix with sub-domains separated by "."
	 */
	protected function prefix() : string
	{
		return $this->domain() . '.property.';
	}
}
