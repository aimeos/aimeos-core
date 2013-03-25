<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 * @version $Id: Factory.php 1320 2012-10-19 19:57:38Z nsendetzky $
 */


/**
 * Factory for stage part in catalog for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Stage_Factory
	extends Client_Html_Common_Factory_Abstract
	implements Client_Html_Common_Factory_Interface
{
	/**
	 * Creates a stage client object.
	 *
	 * @param MShop_Context_Item_Interface $context Shop context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $name Client name (default: "Default")
	 * @return Client_Html_Interface Filter part implementing Client_Html_Interface
	 * @throws Client_Html_Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function createClient( MShop_Context_Item_Interface $context, array $templatePaths, $name = null )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/client/html/catalog/stage/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Client_Html_Catalog_Stage_' . $name : '<not a string>';
			throw new Client_Html_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = 'Client_Html_Interface';
		$classname = 'Client_Html_Catalog_Stage_' . $name;

		return self::_createClient( $context, $classname, $iface, $templatePaths );
	}

}

