ALTER TABLE `matches`
	CHANGE COLUMN `leagueid` `leagueid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `human_players`