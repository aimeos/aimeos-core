<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Address;


/**
 * Common abstract address manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		$prefix = $this->prefix();

		return array_replace( parent::getSaveAttributes(), $this->createAttributes( [
			$prefix . 'parentid' => [
				'label' => 'Address parent ID',
				'internalcode' => 'parentid',
				'type' => 'int',
				'public' => false,
			],
			$prefix . 'type' => [
				'label' => 'Address type',
				'internalcode' => 'type',
			],
			$prefix . 'company' => [
				'label' => 'Address company',
				'internalcode' => 'company',
			],
			$prefix . 'vatid' => [
				'label' => 'Address Vat ID',
				'internalcode' => 'vatid',
			],
			$prefix . 'salutation' => [
				'label' => 'Address salutation',
				'internalcode' => 'salutation',
			],
			$prefix . 'title' => [
				'label' => 'Address title',
				'internalcode' => 'title',
			],
			$prefix . 'firstname' => [
				'label' => 'Address firstname',
				'internalcode' => 'firstname',
			],
			$prefix . 'lastname' => [
				'label' => 'Address lastname',
				'internalcode' => 'lastname',
			],
			$prefix . 'address1' => [
				'label' => 'Address address part one',
				'internalcode' => 'address1',
			],
			$prefix . 'address2' => [
				'label' => 'Address address part two',
				'internalcode' => 'address2',
			],
			$prefix . 'address3' => [
				'label' => 'Address address part three',
				'internalcode' => 'address3',
			],
			$prefix . 'postal' => [
				'label' => 'Address postal',
				'internalcode' => 'postal',
			],
			$prefix . 'city' => [
				'label' => 'Address city',
				'internalcode' => 'city',
			],
			$prefix . 'state' => [
				'label' => 'Address state',
				'internalcode' => 'state',
			],
			$prefix . 'languageid' => [
				'label' => 'Address language',
				'internalcode' => 'langid',
			],
			$prefix . 'countryid' => [
				'label' => 'Address country',
				'internalcode' => 'countryid',
			],
			$prefix . 'telephone' => [
				'label' => 'Address telephone',
				'internalcode' => 'telephone',
			],
			$prefix . 'telefax' => [
				'label' => 'Address telefax',
				'internalcode' => 'telefax',
			],
			$prefix . 'mobile' => [
				'label' => 'Address mobile number',
				'internalcode' => 'mobile',
			],
			$prefix . 'email' => [
				'label' => 'Address email',
				'internalcode' => 'email',
			],
			$prefix . 'website' => [
				'label' => 'Address website',
				'internalcode' => 'website',
			],
			$prefix . 'birthday' => [
				'label' => 'Address birthday',
				'internalcode' => 'birthday',
				'type' => 'date',
			],
			$prefix . 'longitude' => [
				'label' => 'Address longitude',
				'internalcode' => 'longitude',
				'type' => 'float',
			],
			$prefix . 'latitude' => [
				'label' => 'Address latitude',
				'internalcode' => 'latitude',
				'type' => 'float',
			],
			$prefix . 'position' => [
				'label' => 'Address position',
				'internalcode' => 'pos',
				'type' => 'int',
			],
		] ) );
	}
}
