<?php

return array(
	'name' => '<extname>',
	'depends' => array(
		'arcavias-core',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'controller/frontend/src',
		'controller/extjs/src',
		'controller/jobs/src',
	),
	'i18n' => array(
		'client/html' => 'client/html/i18n',
		'mshop' => 'lib/custom/i18n',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/extjs' => 'controller/extjs/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
	),
	'config' => array(
		'mysql' => array(
			'lib/custom/config/common',
			'lib/custom/config/mysql',
		),
	),
	'custom' => array(
		'client/html' => array(
			'client/html/layouts',
		),
		'client/extjs' => array(
			'client/extjs/manifest.jsb2',
		),
		'controller/extjs' => array(
			'controller/extjs/src',
		),
		'controller/jobs' => array(
			'controller/jobs/src',
		),
	),
	'setup' => array(
		'lib/custom/setup',
	),
);
