<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'name' => 'aimeos-core',
	'depends' => array(),
	'include' => array(
		'src',
	),
	'config' => array(
		'config',
	),
	'i18n' => array(
		'controller/common' => 'i18n/controller',
		'mshop/code' => '/i18n/mshop/code',
		'mshop' => 'i18n/mshop',
		'language' => 'i18n/language',
		'currency' => 'i18n/currency',
		'country' => 'i18n/country',
	),
	'setup' => array(
		'setup',
	),
	'template' => array(
		'controller/jobs/templates' => array(
			'templates',
		),
	),
);
