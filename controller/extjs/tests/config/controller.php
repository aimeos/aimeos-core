<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'extjs' => array(
		'attribute' => array(
			'export' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
					),
				),
			),
		),
		'catalog' => array(
			'export' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
					),
				),
			),
		),
		'product' => array(
			'export' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
					),
				),
			),
		),
		'media' => array(
			'standard' => array(
				'enablecheck' => false,
				'mimeicon' => array(
					'directory' => 'tmp/media/mimeicons',
				),
			),
		),
	),
);
