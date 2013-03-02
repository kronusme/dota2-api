CREATE TABLE `additional_units` (
	`slot_id` INT(10) UNSIGNED NOT NULL,
	`unitname` VARCHAR(100) NOT NULL,
	`item_0` INT(10) NOT NULL,
	`item_1` INT(10) NOT NULL,
	`item_2` INT(10) NOT NULL,
	`item_3` INT(10) NOT NULL,
	`item_4` INT(10) NOT NULL,
	`item_5` INT(10) NOT NULL,
	INDEX `FK_additional_units_slots` (`slot_id`),
	CONSTRAINT `FK_additional_units_slots` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`)
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT