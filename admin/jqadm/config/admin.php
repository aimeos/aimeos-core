<?php

return array(
	'jqadm' => array(
		'product' => array(
			'decorators' => array(
				'global' => array( 'Index', 'Cache', 'Page' ),
			),
		),
		'product/category' => array(
			'decorators' => array(
				'local' => array( 'Cache' ),
			),
		),
	),
);