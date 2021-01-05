<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */

return [
	'review' => [
		'customer' => [[
			'customerid' => 'test@example.com', 'refid' => 'test2@example.com', 'ordprodid' => 'CNC/6.00',
			'review.name' => 'test user', 'review.comment' => 'an test@example.com comment',
			'review.response' => 'the test2@example.com reponse', 'review.rating' => 5, 'review.status' => 1
		], [
			'customerid' => 'test@example.com', 'refid' => 'test3@example.com', 'ordprodid' => 'CNE/4.50',
			'review.name' => 'test nick', 'review.comment' => 'an test@example.com comment',
			'review.response' => 'the test3@example.com reponse', 'review.rating' => 1, 'review.status' => 0
		]],
		'product' => [[
			'customerid' => 'test@example.com', 'refid' => 'CNC', 'ordprodid' => 'CNC/6.00',
			'review.name' => 'a customer', 'review.comment' => 'an test@example.com comment',
			'review.response' => 'owner response', 'review.rating' => 0, 'review.status' => 0
		], [
			'customerid' => 'test@example.com', 'refid' => 'CNE', 'ordprodid' => 'CNE/4.50',
			'review.name' => 'a customer', 'review.comment' => 'an test@example.com comment',
			'review.response' => 'owner response', 'review.rating' => 4, 'review.status' => 1
		]],
	],
];
