<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: Bootstrap.php 1357 2012-10-30 11:20:09Z nsendetzky $
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRoutes()
	{
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();


		$routeDefault = new Zend_Controller_Router_Route(
			':site/:controller/:action/:trailing/*',
			array(
				'module' => 'default',
				'controller' => 'catalog',
				'action' => 'list',
				'site' => 'unittest',
				'trailing' => '',
			)
		);
		$router->addRoute( 'routeDefault', $routeDefault );


		$routeMin = new Zend_Controller_Router_Route(
			':site',
			array(
				'module' => 'default',
				'controller' => 'catalog',
				'action' => 'list',
				'site' => 'unittest',
			)
		);
		$router->addRoute( 'routeMin', $routeMin );
	}

}
