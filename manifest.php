<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


return array(
	'name' => 'aimeos-core',
	'depends' => array(),
	'include' => array(
		'lib/mshoplib/src',
		'lib/mwlib/src',
		'client/html/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/jsonadm/src',
		'controller/extjs/src',
		'controller/jobs/src',
		'admin/jqadm/src',
	),
	'config' => array(
		'lib/mshoplib/config',
		'client/html/config',
		'controller/jsonadm/config',
		'admin/jqadm/config',
		'config',
	),
	'i18n' => array(
		'admin' => 'admin/i18n',
		'admin/ext' => 'admin/i18n/ext',
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'client/country' => 'client/i18n/country',
		'client/currency' => 'client/i18n/currency',
		'client/language' => 'client/i18n/language',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/jsonadm' => 'controller/jsonadm/i18n',
		'controller/extjs' => 'controller/extjs/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop/code' => 'lib/mshoplib/i18n/code',
		'mshop' => 'lib/mshoplib/i18n',
	),
	'setup' => array(
		'lib/mshoplib/setup',
	),
	'custom' => array(
		'admin/extjs' => array(
			'admin/extjs/manifest.jsb2',
		),
		'admin/jqadm/templates' => array(
			'admin/jqadm/templates',
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
		'controller/jsonadm/templates' => array(
			'controller/jsonadm/templates',
		),
	),
);
