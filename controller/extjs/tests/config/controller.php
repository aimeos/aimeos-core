<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'extjs' => array(
		'attribute' => array(
			'export' => array(
				'text' => array(
					'default' => array(
						'enablecheck' => false,
						'exportdir' => PATH_TESTS . '/tmp',
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'default' => array(
						'enablecheck' => false,
						'uploaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
		),
		'catalog' => array(
			'export' => array(
				'text' => array(
					'default' => array(
						'enablecheck' => false,
						'exportdir' => PATH_TESTS . '/tmp',
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'default' => array(
						'enablecheck' => false,
						'uploaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
		),
		'product' => array(
			'export' => array(
				'text' => array(
					'default' => array(
						'enablecheck' => false,
						'exportdir' => PATH_TESTS . '/tmp',
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'default' => array(
						'enablecheck' => false,
						'uploaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
		),
		'media' => array(
			'default' => array(
				'enablecheck' => false,
				'basedir' => PATH_TESTS,
				'upload' => array(
					'directory' => 'tmp/media/testdir',
				),
				'mimeicon' => array(
					'directory' => 'tmp/media/mimeicons',
				),
				'command' => array(
					'file' => 'file -b --mime-type %1$s',
					'identify' => 'identify -quiet -format "%%m" %1$s 2>/dev/null',
				),
			),
		),
	),
);
