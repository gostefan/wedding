CREATE TABLE `users` (
	`id`           INT          NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	`singular`     BOOLEAN      NOT NULL DEFAULT TRUE,
	`name`         VARCHAR(200) NOT NULL,
	`email`        VARCHAR(100) NOT NULL UNIQUE,
	`address`      TEXT,
	`dinnerInvite` BOOLEAN NOT  NULL DEFAULT FALSE,
	`username`     VARCHAR(20)  NOT NULL UNIQUE,
	`pwd`          VARCHAR(255) NOT NULL
	`status`       ENUM('NONE','ACCEPT','DECLINE') NOT NULL DEFAULT 'NONE',
	`apero`        BOOLEAN      NOT NULL DEFAULT FALSE,
	`dinner`       BOOLEAN      NOT NULL DEFAULT FALSE,
	`hotel`        BOOLEAN      NOT NULL DEFAULT FALSE,
	`plusOne`      BOOLEAN      NOT NULL DEFAULT FALSE,
	`plusOneName`  VARCHAR(100)          DEFAULT NULL,
	`comments` text
) ENGINE = InnoDB;

CREATE TABLE `images` (
	`name` VARCHAR(100) NOT NULL PRIMARY KEY,
	`path` VARCHAR(100) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE `wishCategories` (
	`id`          INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name`        VARCHAR(100) NOT NULL UNIQUE,
	`imageName`   VARCHAR(100) NOT NULL,
	`order`       INT          NOT NULL DEFAULT 1000
) ENGINE = InnoDB;
ALTER TABLE `wishCategories`
	ADD CONSTRAINT `categoriesImageLink`
	FOREIGN KEY (`imageName`)
	REFERENCES `images`(`name`)
	ON DELETE RESTRICT
	ON UPDATE CASCADE;

CREATE TABLE `wishes` (
	`id`          INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name`        VARCHAR(100) NOT NULL UNIQUE,
	`description` TEXT(1000)   NOT NULL,
	`longDescr`   TEXT(5000),
	`price`       INT          NOT NULL,
	`imageName`   VARCHAR(100) NOT NULL,
	`type`        ENUM('BOTH','INKIND','MONEY_ONLY') NOT NULL DEFAULT 'BOTH',
	`category`    INT          NOT NULL,
	`order`       INT          NOT NULL DEFAULT 1000
) ENGINE = InnoDB;
ALTER TABLE `wishes`
	ADD CONSTRAINT `wishesImageLink`
	FOREIGN KEY (`imageName`)
	REFERENCES `images`(`name`)
	ON DELETE RESTRICT
	ON UPDATE CASCADE;
ALTER TABLE `wishes`
	ADD CONSTRAINT `categoryLink`
	FOREIGN KEY (`category`)
	REFERENCES `wishCategories`(`id`)
	ON DELETE RESTRICT
	ON UPDATE CASCADE;

CREATE TABLE `presents` (
	`userid` INT     NOT NULL,
	`wishid` INT     NOT NULL,
	`amount` INT     NOT NULL,
	`inKind` BOOLEAN NOT NULL
) ENGINE = InnoDB;
ALTER TABLE `presents`
	ADD INDEX `combination` (
		`userid`,
		`wishid`);
ALTER TABLE `presents`
	ADD CONSTRAINT `userId`
	FOREIGN KEY (`userid`)
	REFERENCES `users`(`id`)
	ON DELETE RESTRICT
	ON UPDATE CASCADE;
ALTER TABLE `presents`
	ADD CONSTRAINT `wishId`
	FOREIGN KEY (`wishid`)
	REFERENCES `wishes`(`id`)
	ON DELETE RESTRICT
	ON UPDATE CASCADE;