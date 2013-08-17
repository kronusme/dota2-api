ALTER TABLE `ti2013_matches`
	CHANGE COLUMN `start_time` `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `first_blood_time`;