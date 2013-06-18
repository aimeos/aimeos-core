<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRoutes()
	{
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();


		$route = new Zend_Controller_Router_Route(
			':site/:controller/:action/:a-name/*',
			array(
				'module' => 'default',
				'controller' => 'catalog',
				'action' => 'list',
				'site' => 'unittest',
				'a-name' => '',
			)
		);
		$router->addRoute( 'routeDefault', $route );


		$route = new Zend_Controller_Router_Route(
			':site',
			array(
				'module' => 'default',
				'controller' => 'catalog',
				'action' => 'list',
				'site' => 'unittest',
			)
		);
		$router->addRoute( 'routeMin', $route );
	}

}
