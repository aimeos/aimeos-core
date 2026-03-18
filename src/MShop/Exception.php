<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Generic exception thrown by Aimeos objects if no specialized exception is available.
 *
 * @package MShop
 */
class Exception extends \Exception
{
	public function __construct( string $message = '', int $code = 404, ?\Throwable $previous = null )
	{
		parent::__construct( $message, $code, $previous );
	}
}
