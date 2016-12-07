<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager;


/**
 * Abstract class for all locale manager implementations.
 *
 * @package MShop
 * @subpackage Locale
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Only current site.
	 * Use only the current site ID, not inherited ones or IDs of sub-sites.
	 */
	const SITE_ONE = 0;

	/**
	 * Current site up to root site.
	 * Use all site IDs from the current site up to the root site.
	 */
	const SITE_PATH = 1;

	/**
	 * Current site and sub-sites.
	 * Use all site IDs from the current site and its sub-sites.
	 */
	const SITE_SUBTREE = 2;

	/**
	 * Constant for all other constants.
	 * Use all site IDs from the current site up to the root site but also the
	 * sub-sites of the current site.
	 */
	const SITE_ALL = 3;
}
