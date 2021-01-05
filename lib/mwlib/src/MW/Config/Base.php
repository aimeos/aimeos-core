<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config;


/**
 * Common methods for all configuration classes
 *
 * @package MW
 * @subpackage Config
 */
abstract class Base implements \Aimeos\MW\Config\Iface
{
	private $includeCache = [];


	/**
	 * Includes config files using a simple caching.
	 *
	 * @param string $file Path and file name of a config file
	 * @return array Value of the requested config file
	 **/
	protected function includeFile( string $file ) : array
	{
		if( !isset( $this->includeCache[$file] ) ) {
			$this->includeCache[$file] = include $file;
		}

		return $this->includeCache[$file];
	}
}
