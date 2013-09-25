ALTER TABLE `ability_upgrades`
ADD PRIMARY KEY (`slot_id`, `level`);





ALTER TABLE `matches`
DROP COLUMN `id`;

ALTER TABLE `matches`
DROP PRIMARY KEY,
DROP INDEX `matchid`,
DROP INDEX `FK_matches_game_mods`;

ALTER TABLE `matches`
DROP INDEX `matchid`;

ALTER TABLE `matches`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`match_id`);

ALTER TABLE `matches`
DROP INDEX `FK_matches_game_mods`;