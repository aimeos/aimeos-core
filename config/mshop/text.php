<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return [
	'manager' => [
		'decorators' => [
			'global' => [
				'Lists' => 'Lists',
				'Type' => 'Type',
			]
		],
		'lists' => [
			'submanagers' => [
				'type' => 'type',
			]
		],
		'submanagers' => [
			'lists' => 'lists',
			'type' => 'type',
		],
	],
];
