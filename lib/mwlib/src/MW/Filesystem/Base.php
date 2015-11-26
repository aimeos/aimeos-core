<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Filesystem
 */


namespace Aimeos\MW\Filesystem;


/**
 * Base class for file system adapter
 *
 * @package MW
 * @subpackage Filesystem
 */
class Base
{
	/**
	 * No option flag when creating files
	 * @var integer
	 */
	const OPT_NONE = 0;


	/**
	 * File visibility flag (0: public)
	 * @var integer
	 */
	const OPT_PRIVATE = 1;
}