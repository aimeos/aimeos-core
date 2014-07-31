<?php

return array(
	'set' => '
		REPLACE INTO "madmin_cache" (
			"id", "siteid", "expire", "value"
		) VALUES (
			?, ?, ?, ?
		)
	',
	'settag' => '
		REPLACE INTO "madmin_cache_tag" (
			"tid", "tsiteid", "tname"
		) VALUES :tuples
	',
);