<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation for last seen products.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Seen_Default
	extends Client_Html_Abstract
{
	private $_subPartNames = array();
	private $_subPartPath = 'client/html/catalog/detail/seen/default/subparts';


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		if( isset( $view->detailProductItem ) )
		{
			$context = $this->_getContext();
			$session = $context->getSession();

			$max = $context->getConfig()->get( 'client/html/catalog/session/seen/default/maxitems', 6 );
			$str = $session->get( 'arcavias/client/html/catalog/session/seen' );

			if( ( $lastSeen = @unserialize( $str ) ) === false ) {
				$lastSeen = array();
			}

			$lastSeen[ $view->detailProductItem->getId() ] = $view->detailProductItem;
			$lastSeen = array_slice( $lastSeen, -$max, $max, true );

			$session->set( 'arcavias/client/html/catalog/session/seen', serialize( $lastSeen ) );
		}

		return '';
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		return '';
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'catalog/detail/seen/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}