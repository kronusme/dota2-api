ALTER TABLE `matches`
	CHANGE COLUMN `dire_team_id` `dire_team_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `radiant_team_complete`;