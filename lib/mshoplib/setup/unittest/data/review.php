<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

return [
	'review' => [
		'customer' => [
			[
				'customerid' => 'UTC001', 'refid' => 'UTC002', 'ordprodid' => 'CNC/6.00',
				'review.name' => 'test user', 'review.comment' => 'an UTC001 comment',
				'review.response' => 'the UTC002 reponse', 'review.rating' => 5, 'review.status' => 1
			], [
				'customerid' => 'UTC001', 'refid' => 'UTC003', 'ordprodid' => 'CNE/4.50',
				'review.name' => 'test nick', 'review.comment' => 'an UTC001 comment',
				'review.response' => 'the UTC003 reponse', 'review.rating' => 1, 'review.status' => 0
			]
		],
		'product' => [
			[
				'customerid' => 'UTC001', 'refid' => 'CNC', 'ordprodid' => 'CNC/6.00',
				'review.name' => 'a customer', 'review.comment' => 'an UTC001 comment',
				'review.response' => 'owner response', 'review.rating' => 0, 'review.status' => 0
			], [
				'customerid' => 'UTC001', 'refid' => 'CNE', 'ordprodid' => 'CNE/4.50',
				'review.name' => 'a customer', 'review.comment' => 'an UTC001 comment',
				'review.response' => 'owner response', 'review.rating' => 4, 'review.status' => 1
			],
		],
	],
];
