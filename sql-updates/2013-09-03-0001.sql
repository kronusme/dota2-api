CREATE TABLE `leagues` (
  `leagueid` INT(4) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL DEFAULT '',
  `description` VARCHAR(2000) NOT NULL DEFAULT '',
  `tournament_url` VARCHAR(200) NULL DEFAULT '',
  `itemdef` INT(11) NULL DEFAULT NULL,
  `is_finished` TINYINT(4) NULL DEFAULT '0',
  PRIMARY KEY (`leagueid`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB;

ALTER TABLE `matches`
ADD CONSTRAINT `FK_matches_leagues` FOREIGN KEY (`leagueid`) REFERENCES `leagues` (`leagueid`);

ALTER TABLE `picks_bans`
ADD CONSTRAINT `FK_picks_bans_matches` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`);
