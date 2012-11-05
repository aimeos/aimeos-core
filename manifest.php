<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: manifest.php 14572 2011-12-23 14:26:31Z nsendetzky $
 */


return array(
	'name' => 'Arcavias',
	'description' => 'Arcavias core system with admin interface',
	'author' => 'Metaways Infosystems GmbH',
	'email' => 'application@metaways.de',
	'version' => '2012-11',
	'depends' => array(
	),
	'conflicts' => array(
	),
	'include' => array(
		'lib/mshoplib/src',
		'controller/frontend/src',
		'client/html/src',
		'controller/extjs/src',
		'lib/mwlib',
		'lib/zendlib',
		'lib/phpexcel',
	),
	'config' => array(
		'mysql' => array(
			'lib/mshoplib/config/common',
			'lib/mshoplib/config/mysql',
			'controller/frontend/config/controller',
			'controller/extjs/config/controller',
			'config',
		),
	),
	'setup' => array(
		'lib/mshoplib/setup',
	),
	'custom' => array(
		'client/extjs' => array(
			'client/extjs/manifest.jsb2',
		),
		'client/html' => array(
			'client/html/layouts',
		),
	),
);
