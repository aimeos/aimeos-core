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
						'exportdir' => PATH_TESTS . '/tmp',
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'uploaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
		),
		'catalog' => array(
			'export' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'exportdir' => PATH_TESTS . '/tmp',
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'uploaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
		),
		'product' => array(
			'export' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'exportdir' => PATH_TESTS . '/tmp',
						'downloaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
			'import' => array(
				'text' => array(
					'standard' => array(
						'enablecheck' => false,
						'uploaddir' => PATH_TESTS . '/tmp',
					),
				),
			),
		),
		'media' => array(
			'standard' => array(
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
