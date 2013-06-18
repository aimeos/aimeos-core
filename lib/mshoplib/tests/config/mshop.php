<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


return array(
	'coupon' => array(
		'provider' => array(
			'decorators' => array(
				'Example',
			),
		),
	),
	'product' => array(
		'manager' => array(
			'decorators' => array(
				'global' => array(
					'Changelog',
				),
			),
		),
	),
	'service' => array(
		'provider' => array(
			'delivery' => array(
				'decorators' => array(
				),
			),
		),
	),
);