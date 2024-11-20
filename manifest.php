<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */


return [
	'name' => 'aimeos-core',
	'depends' => [],
	'include' => [
		'src',
	],
	'config' => [
		'config',
	],
	'i18n' => [
		'mshop/code' => '/i18n/mshop/code',
		'mshop' => 'i18n/mshop',
		'language' => 'i18n/language',
		'currency' => 'i18n/currency',
		'country' => 'i18n/country',
	],
	'setup' => [
		'setup',
	],
	'template' => [
		'controller/jobs/templates' => [
			'templates',
		],
	],
];
