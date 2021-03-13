<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Manager;


/**
 * Rule manager interface
 * @package MShop
 * @subpackage Rule
 */
interface Iface extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Returns the rule provider which is responsible for the rule item
	 *
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 * @param string $type Rule type code
	 * @return \Aimeos\MShop\Rule\Provider\Iface Returns the decoratad rule provider object
	 * @throws \Aimeos\MShop\Rule\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Rule\Item\Iface $item, string $type ) : \Aimeos\MShop\Rule\Provider\Iface;
}
