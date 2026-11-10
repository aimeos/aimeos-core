<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Exception thrown if the authorization policy denies an action
 *
 * @package MShop
 */
class PolicyException extends \Aimeos\MShop\Exception
{
	public function __construct( string $message = '', int $code = 403, ?\Throwable $previous = null )
	{
		parent::__construct( $message, $code, $previous );
	}
}
