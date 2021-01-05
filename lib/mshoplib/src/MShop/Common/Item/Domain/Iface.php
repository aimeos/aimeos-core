<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Domain;


/**
 * Interface for items which are referenced by other domains
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the domain name the item is stored for
	 *
	 * @return string Domain name e.g. catalog, product, service, ...
	 */
	public function getDomain() : string;

	/**
	 * Set the domain name the item is stored for
	 *
	 * @param string $domain Domain name e.g. catalog, product, service, ...
	 * @return \Aimeos\MShop\Common\Item\Iface Item object for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Common\Item\Iface;
}
