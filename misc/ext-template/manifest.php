<?php

return array(
	'name' => '<extname>',
	'depends' => array(
		'aimeos-core',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'client/jqadm/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/jsonadm/src',
		'controller/extjs/src',
		'controller/jobs/src',
	),
	'i18n' => array(
		'client/html' => 'client/html/i18n',
		'client/jqadm' => 'client/jqadm/i18n',
		'mshop' => 'lib/custom/i18n',
		'controller/common' => 'controller/common/i18n',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/jsonadm' => 'controller/jsonadm/i18n',
		'controller/extjs' => 'controller/extjs/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
	),
	'config' => array(
		'lib/custom/config',
	),
	'custom' => array(
		'client/html' => array(
			'client/html/templates',
		),
		'client/jqadm/templates' => array(
			'client/jqadm/templates',
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
		'controller/jobs/templates' => array(
			'controller/jobs/templates',
		),
		'controller/jsonadm/templates' => array(
			'controller/jsonadm/templates',
		),
	),
	'setup' => array(
		'lib/custom/setup',
	),
);
