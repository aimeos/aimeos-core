<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for building URLs using Zend Router.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Url_Zend
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_router;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param Zend_Controller_Router_Interface $router Zend Router implementation
	 */
	public function __construct( $view, Zend_Controller_Router_Interface $router )
	{
		parent::__construct( $view );

		$this->_router = $router;
	}


	/**
	 * Returns the URL assembled from the given arguments.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param array $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( $target = null, $controller = null, $action = null, array $params = array(), array $trailing = array() )
	{
		$paramList = array( 'controller' => $controller, 'action' => $action );

		// Slashes in URL parameters confuses the router
		foreach( $params as $key => $value ) {
			$paramList[$key] = str_replace( '/', '', $value );
		}

		if( !empty( $trailing ) ) {
			$paramList['trailing'] = str_replace( '/', '-', join( '-', $trailing ) );
		}

		return $this->_router->assemble( $paramList, $target, true );
	}
}