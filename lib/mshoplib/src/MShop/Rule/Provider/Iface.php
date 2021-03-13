<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider;


/**
 * Rule provider interface for dealing with run-time loadable extensions.
 *
 * @package MShop
 * @subpackage Rule
 */
interface Iface
{
	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes ) : array;

	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array;

	/**
	 * Injects the outer object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Rule\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Rule\Provider\Iface Rule object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Rule\Provider\Iface $object ) : \Aimeos\MShop\Rule\Provider\Iface;
}
