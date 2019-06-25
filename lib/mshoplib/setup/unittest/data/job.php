<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return [
	'job' => [
		'job/unittest job' => [
			'job.label' => 'unittest job', 'job.method' => 'controller.method',
			'job.parameter' => ['items' => 'testfile.ext'], 'job.result' => ['items' => 'testfile.ext'],
			'job.status' => 0
		],
	]
];
