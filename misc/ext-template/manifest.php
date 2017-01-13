<?php

return array(
	'name' => '<extname>',
	'depends' => array(
		'aimeos-core',
		'ai-admin-extadm',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-controller-jobs',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/extjs/src',
		'controller/jobs/src',
		'admin/jsonadm/src',
		'admin/jqadm/src',
	),
	'i18n' => array(
		'admin' => 'admin/i18n',
		'admin/jsonadm' => 'admin/jsonadm/i18n',
		'controller/common' => 'controller/common/i18n',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/extjs' => 'controller/extjs/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop' => 'lib/custom/i18n',
		'client' => 'client/i18n',
	),
	'config' => array(
		'lib/custom/config',
	),
	'custom' => array(
		'admin/jsonadm/templates' => array(
			'admin/jsonadm/templates',
		),
		'admin/jqadm/templates' => array(
			'admin/jqadm/templates',
		),
		'admin/extjs' => array(
			'admin/extjs/manifest.jsb2',
		),
		'client/html/templates' => array(
			'client/html/templates',
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
	),
	'setup' => array(
		'lib/custom/setup',
	),
);
