<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


return array(
	'name' => 'Arcavias',
	'description' => 'Arcavias core system with admin interface',
	'author' => 'Metaways Infosystems GmbH',
	'email' => 'application@metaways.de',
	'version' => '2013-05',
	'depends' => array(
	),
	'conflicts' => array(
	),
	'include' => array(
		'lib/mshoplib/src',
		'lib/mwlib/src',
		'client/html/src',
		'controller/frontend/src',
		'controller/extjs/src',
		'vendor/codeplex/phpexcel',
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
	'i18n' => array(
		'client/html' => 'client/html/i18n',
		'client/html/code' => 'client/html/i18n/code',
		'client/html/country' => 'client/html/i18n/country',
		'client/html/currency' => 'client/html/i18n/currency',
		'client/html/language' => 'client/html/i18n/language',
		'controller/frontend' => 'controller/frontend/i18n',
		'mshop' => 'lib/mshoplib/i18n',
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
