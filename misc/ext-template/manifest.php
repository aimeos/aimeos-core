<?php

return [
	'name' => '<extname>',
	'config' => [
		'config',
	],
	'depends' => [
		'aimeos-core',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-client-jsonapi',
		'ai-controller-jobs',
		'ai-controller-frontend',
	],
	'include' => [
		'lib/custom/src',
		'client/html/src',
		'client/jsonapi/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/jobs/src',
		'admin/jsonadm/src',
		'admin/jqadm/src',
	],
	'i18n' => [
		'admin' => 'admin/i18n',
		'admin/jsonadm' => 'admin/jsonadm/i18n',
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'controller/common' => 'controller/common/i18n',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop' => 'lib/custom/i18n',
	],
	'setup' => [
		'lib/custom/setup',
	],
	'template' => [
		'admin/jqadm/templates' => [
			'admin/jqadm/templates',
		],
		'admin/jsonadm/templates' => [
			'admin/jsonadm/templates',
		],
		'client/html/templates' => [
			'client/html/templates',
		],
		'client/jsonapi/templates' => [
			'client/jsonapi/templates',
		],
		'controller/jobs/templates' => [
			'controller/jobs/templates',
			'client/html/templates',
		],
	],
	'custom' => [
		'admin/jqadm' => [
			'admin/jqadm/manifest.jsb2',
		],
		'controller/jobs' => [
			'controller/jobs/src',
		],
	],
];
