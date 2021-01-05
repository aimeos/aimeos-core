<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue\Message;


/**
 * Default message implementation
 *
 * @package MW
 * @subpackage MQueue
 */
class Standard implements Iface
{
	private $values;


	/**
	 * Initializes the message object
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values )
	{
		$this->values = $values;
	}


	/**
	 * Returns the message body
	 *
	 * @return string Message body
	 */
	public function getBody() : string
	{
		return ( isset( $this->values['message'] ) ? $this->values['message'] : '' );
	}


	/**
	 * Returns the message ID
	 *
	 * @return integer|null Message ID
	 */
	public function getId() : ?string
	{
		return ( isset( $this->values['id'] ) ? $this->values['id'] : null );
	}
}
