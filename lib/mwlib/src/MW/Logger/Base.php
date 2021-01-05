<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Base logger class defining required error level constants
 *
 * @package MW
 * @subpackage Logger
 */
abstract class Base
{
	/**
	 * Emergency (0): system is unusable
	 */
	const EMERG = 0;

	/**
	 * Alert (1): action must be taken immediately
	 */
	const ALERT = 1;

	/**
	 * Critical (2): critical conditions
	 */
	const CRIT = 2;

	/**
	 * Error (3): error conditions
	 */
	const ERR = 3;

	/**
	 * Warning (4): warning conditions
	 */
	const WARN = 4;

	/**
	 * Notice (5): normal but significant condition
	 */
	const NOTICE = 5;

	/**
	 * Informational (6): informational messages
	 */
	const INFO = 6;

	/**
	 * Debug (7): debug messages
	 */
	const DEBUG = 7;


	/**
	 * Checks if the given log constant is valid
	 *
	 * @param int $level Log constant
	 * @return mixed Log level
	 * @throws \Aimeos\MW\Logger\Exception If log constant is unknown
	 */
	protected function getLogLevel( int $level )
	{
		switch( $level )
		{
			case self::EMERG: return 'emergency';
			case self::ALERT: return 'alert';
			case self::CRIT: return 'critical';
			case self::ERR: return 'error';
			case self::WARN: return 'warning';
			case self::NOTICE: return 'notice';
			case self::INFO: return 'info';
			case self::DEBUG: return 'debug';
		}

		throw new \Aimeos\MW\Logger\Exception( sprintf( 'Invalid log level constant "%1$d"', $level ) );
	}
}
