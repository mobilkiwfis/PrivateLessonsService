CREATE TABLE `users` (
	`user_id` int(11) NOT NULL AUTO_INCREMENT,
	`firstname` varchar(100) NOT NULL,
	`surname` varchar(100) NOT NULL,
	`password` varchar(128) NOT NULL,
	`email` varchar(100) NOT NULL UNIQUE,
	`photo` varchar(128) NOT NULL,
	`phone_number` varchar(30) NOT NULL,
	`creation_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`is_activated` bool NOT NULL DEFAULT '0',
	`activation_key` varchar(128) NOT NULL UNIQUE,
	`is_banned` bool NOT NULL DEFAULT '0',
	PRIMARY KEY (`user_id`)
);

CREATE TABLE `offers` (
	`offer_id` int(11) NOT NULL AUTO_INCREMENT,
	`owner_id` int(11) NOT NULL,
	`is_active` bool NOT NULL DEFAULT '1',
	`is_archived` bool NOT NULL DEFAULT '0',
	`description` varchar(1000) NOT NULL,
	`category_id` int(11) NOT NULL,
	`price` FLOAT NOT NULL,
	`localization` varchar(256) NOT NULL,
	`at_teachers_house` bool NOT NULL DEFAULT '0',
	`at_students_house` bool NOT NULL DEFAULT '0',
	`get_to_student_for_free` bool NOT NULL DEFAULT '0',
	`creation_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`last_modification_timestamp` TIMESTAMP,
	`visibility_expire_timestamp` TIMESTAMP,
	`promoted_expire_timestamp` TIMESTAMP,
	PRIMARY KEY (`offer_id`)
);

CREATE TABLE `categories` (
	`category_id` int(11) NOT NULL AUTO_INCREMENT,
	`subcategory_of` int(11),
	`category_key` varchar(100) NOT NULL UNIQUE,
	PRIMARY KEY (`category_id`)
);

CREATE TABLE `available_days` (
	`offer_id` int(11) NOT NULL,
	`mo_morning` bool NOT NULL DEFAULT '0',
	`mo_evening` bool NOT NULL DEFAULT '0',
	`tu_morning` bool NOT NULL DEFAULT '0',
	`tu_evening` bool NOT NULL DEFAULT '0',
	`we_morning` bool NOT NULL DEFAULT '0',
	`we_evening` bool NOT NULL DEFAULT '0',
	`th_morning` bool NOT NULL DEFAULT '0',
	`th_evening` bool NOT NULL DEFAULT '0',
	`fr_morning` bool NOT NULL DEFAULT '0',
	`fr_evening` bool NOT NULL DEFAULT '0',
	`sa_morning` bool NOT NULL DEFAULT '0',
	`sa_evening` bool NOT NULL DEFAULT '0',
	`su_morning` bool NOT NULL DEFAULT '0',
	`su_evening` bool NOT NULL DEFAULT '0',
	PRIMARY KEY (`offer_id`)
);

CREATE TABLE `payments_history` (
	`payment_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`offer_id` int(11) NOT NULL,
	`charge` FLOAT NOT NULL,
	`payment_method` varchar(100) NOT NULL,
	`payment_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`start_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`end_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`payment_id`)
);

CREATE TABLE `favourites` (
	`offer_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL
);

CREATE TABLE `teaching_levels` (
	`offer_id` int(11) NOT NULL,
	`elementary_school` bool NOT NULL DEFAULT '0',
	`junior_high_school` bool NOT NULL DEFAULT '0',
	`high_school` bool NOT NULL DEFAULT '0',
	`vocational_school` bool NOT NULL DEFAULT '0',
	`college` bool NOT NULL DEFAULT '0',
	`other` bool NOT NULL DEFAULT '0',
	PRIMARY KEY (`offer_id`)
);

ALTER TABLE `offers` ADD CONSTRAINT `offers_fk0` FOREIGN KEY (`owner_id`) REFERENCES `users`(`user_id`);

ALTER TABLE `offers` ADD CONSTRAINT `offers_fk1` FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`);

ALTER TABLE `categories` ADD CONSTRAINT `categories_fk0` FOREIGN KEY (`subcategory_of`) REFERENCES `categories`(`category_id`);

ALTER TABLE `available_days` ADD CONSTRAINT `available_days_fk0` FOREIGN KEY (`offer_id`) REFERENCES `offers`(`offer_id`);

ALTER TABLE `payments_history` ADD CONSTRAINT `payments_history_fk0` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`);

ALTER TABLE `payments_history` ADD CONSTRAINT `payments_history_fk1` FOREIGN KEY (`offer_id`) REFERENCES `offers`(`offer_id`);

ALTER TABLE `favourites` ADD CONSTRAINT `favourites_fk0` FOREIGN KEY (`offer_id`) REFERENCES `offers`(`offer_id`);

ALTER TABLE `favourites` ADD CONSTRAINT `favourites_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`);

ALTER TABLE `teaching_levels` ADD CONSTRAINT `teaching_levels_fk0` FOREIGN KEY (`offer_id`) REFERENCES `offers`(`offer_id`);
