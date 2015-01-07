<?php

return array(
	'jobs' => array(
		'product' => array(
			'export' => array(
				'location' => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'tmp',
				'max-items' => 15,
				'max-query' => 5,
				'sitemap' => array(
					'location' => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'tmp',
					'max-items' => 15,
					'max-query' => 5,
				),
			),
		),
	),
);