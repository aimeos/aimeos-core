<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */


return [
	'manager' => [
		'decorators' => [
			'global' => [
				'Lists' => 'Lists',
				'Property' => 'Property',
				'Address' => 'Address',
			]
		],
		'lists' => [
			'submanagers' => [
				'type' => 'type',
			]
		],
		'property' => [
			'decorators' => [
				'global' => [
					'Type' => 'Type',
				]
			],
			'submanagers' => [
				'type' => 'type',
			]
		],
		'submanagers' => [
			'address' => 'address',
			'lists' => 'lists',
			'property' => 'property',
		],
		'address' => [
			'clear' => [
				'ansi' => '
					DELETE FROM ":table"
					WHERE :cond AND "siteid" LIKE ?
				'
			],
			'delete' => [
				'ansi' => '
					DELETE FROM ":table"
					WHERE :cond AND ( "siteid" LIKE ? OR "siteid" = ? )
				'
			],
			'insert' => [
				'ansi' => '
					INSERT INTO ":table" ( :names
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?
					)
				'
			],
			'update' => [
				'ansi' => '
					UPDATE ":table"
					SET :names
						"mtime" = ?, "editor" = ?
					WHERE ( "siteid" LIKE ? OR "siteid" = ? ) AND "id" = ?
				'
			],
		],
		'aggregate' => [
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_customer" mcus
					:joins
					WHERE :cond
					GROUP BY mcus.id, :cols, :val
					ORDER BY mcus.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_customer" mcus
					:joins
					WHERE :cond
					GROUP BY mcus.id, :cols, :val
					ORDER BY mcus.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		],
		'clear' => [
			'ansi' => '
				DELETE FROM "mshop_customer"
				WHERE :cond AND "siteid" LIKE ?
			',
		],
		'delete' => [
			'ansi' => '
				DELETE FROM "mshop_customer"
				WHERE :cond AND ( "siteid" LIKE ? OR "siteid" = ? )
			'
		],
		'insert' => [
			'ansi' => '
				INSERT INTO "mshop_customer" ( :names
					"label", "code", "company", "vatid", "salutation", "title",
					"firstname", "lastname", "address1", "address2", "address3",
					"postal", "city", "state", "countryid", "langid", "telephone",
					"mobile", "email", "telefax", "website", "longitude", "latitude", "birthday",
					"status", "vdate", "password", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
				)
			'
		],
		'update' => [
			'ansi' => '
				UPDATE "mshop_customer"
				SET :names
					"label" = ?, "code" = ?, "company" = ?, "vatid" = ?,
					"salutation" = ?, "title" = ?, "firstname" = ?, "lastname" = ?,
					"address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?,
					"city" = ?, "state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?,
					"mobile" = ?, "email" = ?, "telefax" = ?, "website" = ?,
					"longitude" = ?, "latitude" = ?, "birthday" = ?, "status" = ?,
					"vdate" = ?, "password" = ?, "mtime" = ?, "editor" = ?
				WHERE ( "siteid" LIKE ? OR "siteid" = ? ) AND "id" = ?
			'
		],
		'newid' => [
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_customer_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		],
	],
];
