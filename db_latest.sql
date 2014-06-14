CREATE TABLE `leagues` (
  `leagueid` mediumint(4) unsigned NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `description` varchar(2000) NOT NULL DEFAULT '',
  `tournament_url` varchar(200) DEFAULT '',
  `itemdef` int(11) DEFAULT NULL,
  PRIMARY KEY (`leagueid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `league_prize_pools` (
  `league_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  `prize_pool` INT(10) UNSIGNED NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`league_id`, `date`),
  CONSTRAINT `FK_league_prize_pools_leagues` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`leagueid`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB;

CREATE TABLE `matches` (
  `match_id` int(20) unsigned NOT NULL,
  `season` tinyint(4) unsigned DEFAULT NULL,
  `radiant_win` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `duration` smallint(11) unsigned NOT NULL DEFAULT '0',
  `first_blood_time` smallint(11) unsigned NOT NULL DEFAULT '0',
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `match_seq_num` bigint(20) unsigned DEFAULT NULL,
  `game_mode` tinyint(4) NOT NULL,
  `tower_status_radiant` int(11) unsigned NOT NULL DEFAULT '0',
  `tower_status_dire` int(11) unsigned NOT NULL DEFAULT '0',
  `barracks_status_radiant` int(11) unsigned NOT NULL DEFAULT '0',
  `barracks_status_dire` int(11) unsigned NOT NULL DEFAULT '0',
  `replay_salt` tinyint(4) DEFAULT NULL,
  `lobby_type` tinyint(6) unsigned NOT NULL DEFAULT '0',
  `human_players` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `leagueid` mediumint(4) unsigned NOT NULL DEFAULT '0',
  `cluster` smallint(6) unsigned NOT NULL DEFAULT '0',
  `positive_votes` int(11) unsigned NOT NULL DEFAULT '0',
  `negative_votes` int(11) unsigned NOT NULL DEFAULT '0',
  `radiant_team_id` int(11) unsigned DEFAULT NULL,
  `radiant_name` varchar(200) DEFAULT NULL,
  `radiant_logo` varchar(32) DEFAULT NULL,
  `radiant_team_complete` tinyint(3) unsigned DEFAULT NULL,
  `dire_team_id` int(11) unsigned DEFAULT NULL,
  `dire_name` varchar(200) DEFAULT NULL,
  `dire_logo` varchar(32) DEFAULT NULL,
  `dire_team_complete` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`match_id`),
  INDEX `FK_matches_leagues` (`leagueid`),
  CONSTRAINT `FK_matches_leagues` FOREIGN KEY (`leagueid`) REFERENCES `leagues` (`leagueid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `account_id` int(20) unsigned NOT NULL DEFAULT '0',
  `personaname` varchar(50) NOT NULL DEFAULT '',
  `steamid` varchar(64) NOT NULL DEFAULT '',
  `avatar` varchar(200) NOT NULL,
  `profileurl` varchar(128) NOT NULL,
  `is_personaname_real` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `slots` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `match_id` int(20) unsigned NOT NULL DEFAULT '0',
  `account_id` int(20) unsigned NOT NULL DEFAULT '0',
  `hero_id` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `player_slot` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `item_0` smallint(10) NOT NULL DEFAULT '0',
  `item_1` smallint(10) NOT NULL DEFAULT '0',
  `item_2` smallint(10) NOT NULL DEFAULT '0',
  `item_3` smallint(10) NOT NULL DEFAULT '0',
  `item_4` smallint(10) NOT NULL DEFAULT '0',
  `item_5` smallint(10) NOT NULL DEFAULT '0',
  `kills` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `deaths` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `assists` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `leaver_status` tinyint(10) NOT NULL DEFAULT '0',
  `gold` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `last_hits` smallint(10) unsigned NOT NULL DEFAULT '0',
  `denies` smallint(10) unsigned NOT NULL DEFAULT '0',
  `gold_per_min` smallint(10) unsigned NOT NULL DEFAULT '0',
  `xp_per_min` smallint(10) unsigned NOT NULL DEFAULT '0',
  `gold_spent` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `hero_damage` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `tower_damage` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `hero_healing` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `FK_slots_users` (`account_id`),
  INDEX `FK_slots_matches` (`match_id`),
  CONSTRAINT `FK_slots_matches` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`),
  CONSTRAINT `FK_slots_users` FOREIGN KEY (`account_id`) REFERENCES `users` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ability_upgrades` (
  `slot_id` mediumint(10) unsigned NOT NULL,
  `ability` smallint(8) unsigned NOT NULL,
  `time` smallint(10) unsigned NOT NULL,
  `level` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`slot_id`,`level`),
  KEY `FK_ability_upgrades_slots` (`slot_id`),
  CONSTRAINT `FK_ability_upgrades_slots` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `additional_units` (
  `slot_id` mediumint(10) unsigned NOT NULL,
  `unitname` varchar(100) NOT NULL,
  `item_0` smallint(10) unsigned NOT NULL,
  `item_1` smallint(10) unsigned NOT NULL,
  `item_2` smallint(10) unsigned NOT NULL,
  `item_3` smallint(10) unsigned NOT NULL,
  `item_4` smallint(10) unsigned NOT NULL,
  `item_5` smallint(10) unsigned NOT NULL,
  KEY `FK_additional_units_slots` (`slot_id`),
  CONSTRAINT `FK_additional_units_slots` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `picks_bans` (
  `id` mediumint(20) unsigned NOT NULL AUTO_INCREMENT,
  `match_id` int(20) unsigned NOT NULL,
  `is_pick` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hero_id` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `team` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `FK_picks_bans_matches` (`match_id`),
  CONSTRAINT `FK_picks_bans_matches` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;