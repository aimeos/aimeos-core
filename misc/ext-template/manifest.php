<?php

return array(
	'name' => '<extname>',
	'depends' => array(
		'aimeos-core',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-client-jsonapi',
		'ai-controller-jobs',
		'ai-controller-frontend',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'client/jsonapi/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/jobs/src',
		'admin/jsonadm/src',
		'admin/jqadm/src',
	),
	'i18n' => array(
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'admin' => 'admin/i18n',
		'admin/jsonadm' => 'admin/jsonadm/i18n',
		'controller/common' => 'controller/common/i18n',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop' => 'lib/custom/i18n',
	),
	'config' => array(
		'config',
	),
	'custom' => array(
		'admin/jsonadm/templates' => array(
			'admin/jsonadm/templates',
		),
		'admin/jqadm/templates' => array(
			'admin/jqadm/templates',
		),
		'client/html/templates' => array(
			'client/html/templates',
		),
		'client/jsonapi/templates' => array(
			'client/jsonapi/templates',
		),
		'controller/jobs' => array(
			'controller/jobs/src',
		),
		'controller/jobs/templates' => array(
			'controller/jobs/templates',
			'client/html/templates',
		),
	),
	'setup' => array(
		'lib/custom/setup',
	),
);
