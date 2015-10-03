<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Factory for payment email HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Email_Payment_Factory
	extends Client_Html_Common_Factory_Base
	implements Client_Html_Common_Factory_Iface
{
	/**
	 * Creates a payment email client object.
	 *
	 * @param MShop_Context_Item_Iface $context Shop context instance with necessary objects
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string|null $name Client name (default: "Default")
	 * @return Client_Html_Iface Filter part implementing Client_Html_Iface
	 * @throws Client_Html_Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function createClient( MShop_Context_Item_Iface $context, array $templatePaths, $name = null )
	{
		/** classes/client/html/email/payment/name
		 * Class name of the used email payment client implementation
		 *
		 * Each default HTML client can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  Client_Html_Email_Payment_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  Client_Html_Email_Payment_Mypayment
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/client/html/email/payment/name = Mypayment
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyPayment"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/client/html/email/payment/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Client_Html_Email_Payment_' . $name : '<not a string>';
			throw new Client_Html_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Client_Html_Iface';
		$classname = 'Client_Html_Email_Payment_' . $name;

		$client = self::createClientBase( $context, $classname, $iface, $templatePaths );

		return self::addClientDecorators( $context, $client, $templatePaths, 'email/payment' );
	}
}

