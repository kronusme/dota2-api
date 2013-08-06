ALTER TABLE `matches`
	CHANGE COLUMN `leagueid` `leagueid` INT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER `human_players`;
