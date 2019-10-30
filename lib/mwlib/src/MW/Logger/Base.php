<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * @param integer $level Log constant
	 * @throws \Aimeos\MW\Logger\Exception If log constant is unknown
	 */
	protected function getLogLevel( $level )
	{
		switch( $level )
		{
			case \Aimeos\MW\Logger\Base::EMERG: return 'emergency';
			case \Aimeos\MW\Logger\Base::ALERT: return 'alert';
			case \Aimeos\MW\Logger\Base::CRIT: return 'critical';
			case \Aimeos\MW\Logger\Base::ERR: return 'error';
			case \Aimeos\MW\Logger\Base::WARN: return 'warning';
			case \Aimeos\MW\Logger\Base::NOTICE: return 'notice';
			case \Aimeos\MW\Logger\Base::INFO: return 'info';
			case \Aimeos\MW\Logger\Base::DEBUG: return 'debug';
		}

		throw new \Aimeos\MW\Logger\Exception( sprintf( 'Invalid log level constant "%1$d"', $level ) );
	}
}
