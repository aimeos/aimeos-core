<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_supplier_address"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_supplier_address" ("siteid", "refid", "company","salutation","title",
				"firstname","lastname","address1","address2","address3","postal","city","state",
				"countryid","langid","telephone","email","telefax","website","flag","pos", "mtime", "editor", "ctime")
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_supplier_address"
			SET "siteid" = ?, "refid" = ?, "company" = ?, "salutation" = ?, "title" = ?, "firstname" = ?, "lastname" = ?,
				"address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?, "city" = ?, "state" = ?, "countryid" = ?,
				"langid" = ?, "telephone" = ?, "email" = ?, "telefax" = ?, "website" = ?, "flag" = ?, "pos" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT msupad."id", msupad."refid", msupad."company", msupad."salutation", msupad."title",
				   msupad."firstname", msupad."lastname", msupad."address1", msupad."address2", msupad."address3",
				   msupad."postal", msupad."city", msupad."state", msupad."countryid", msupad."langid", msupad."telephone",
				   msupad."email", msupad."telefax", msupad."website", msupad."flag", msupad."pos",
				   msupad."mtime", msupad."editor", msupad."ctime"
			FROM "mshop_supplier_address" AS msupad
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT msupad."id"
				FROM "mshop_supplier_address" AS msupad
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);