-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema campushub
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema campushub
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `campushub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `campushub` ;

-- -----------------------------------------------------
-- Table `campushub`.`admins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`admins` (
  `admin_id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE INDEX `username` (`username` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`announcements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`announcements` (
  `announcement_id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `content` TEXT NOT NULL,
  `posted_by` INT NULL DEFAULT NULL,
  `posted_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_id`),
  INDEX `posted_by` (`posted_by` ASC) VISIBLE,
  CONSTRAINT `announcements_ibfk_1`
    FOREIGN KEY (`posted_by`)
    REFERENCES `campushub`.`admins` (`admin_id`)
    ON DELETE SET NULL)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`clubs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`clubs` (
  `club_id` INT NOT NULL AUTO_INCREMENT,
  `club_name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `logo` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`club_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`event_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`event_categories` (
  `category_id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE INDEX `category_name` (`category_name` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`events` (
  `event_id` INT NOT NULL AUTO_INCREMENT,
  `club_id` INT NULL DEFAULT NULL,
  `category_id` INT NULL DEFAULT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NULL DEFAULT NULL,
  `venue` VARCHAR(150) NULL DEFAULT NULL,
  `poster_image` VARCHAR(255) NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  INDEX `club_id` (`club_id` ASC) VISIBLE,
  INDEX `category_id` (`category_id` ASC) VISIBLE,
  INDEX `created_by` (`created_by` ASC) VISIBLE,
  CONSTRAINT `events_ibfk_1`
    FOREIGN KEY (`club_id`)
    REFERENCES `campushub`.`clubs` (`club_id`)
    ON DELETE SET NULL,
  CONSTRAINT `events_ibfk_2`
    FOREIGN KEY (`category_id`)
    REFERENCES `campushub`.`event_categories` (`category_id`)
    ON DELETE SET NULL,
  CONSTRAINT `events_ibfk_3`
    FOREIGN KEY (`created_by`)
    REFERENCES `campushub`.`admins` (`admin_id`)
    ON DELETE SET NULL)
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`institutions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`institutions` (
  `institution_id` INT NOT NULL AUTO_INCREMENT,
  `institution_name` VARCHAR(150) NOT NULL,
  `location` VARCHAR(150) NULL DEFAULT NULL,
  PRIMARY KEY (`institution_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`media` (
  `media_id` INT NOT NULL AUTO_INCREMENT,
  `event_id` INT NULL DEFAULT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(20) NULL DEFAULT NULL,
  `uploaded_by` INT NULL DEFAULT NULL,
  `uploaded_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`media_id`),
  INDEX `event_id` (`event_id` ASC) VISIBLE,
  CONSTRAINT `media_ibfk_1`
    FOREIGN KEY (`event_id`)
    REFERENCES `campushub`.`events` (`event_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 42
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`students`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`students` (
  `student_id` INT NOT NULL AUTO_INCREMENT,
  `institution_id` INT NULL DEFAULT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `contact_no` VARCHAR(20) NULL DEFAULT NULL,
  `profile_photo` VARCHAR(255) NULL DEFAULT NULL,
  `registered_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`),
  UNIQUE INDEX `email` (`email` ASC) VISIBLE,
  INDEX `institution_id` (`institution_id` ASC) VISIBLE,
  CONSTRAINT `students_ibfk_1`
    FOREIGN KEY (`institution_id`)
    REFERENCES `campushub`.`institutions` (`institution_id`)
    ON DELETE SET NULL)
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`registrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`registrations` (
  `registration_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `registered_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(20) NULL DEFAULT 'Confirmed',
  PRIMARY KEY (`registration_id`),
  UNIQUE INDEX `student_id` (`student_id` ASC, `event_id` ASC) VISIBLE,
  INDEX `event_id` (`event_id` ASC) VISIBLE,
  CONSTRAINT `registrations_ibfk_1`
    FOREIGN KEY (`student_id`)
    REFERENCES `campushub`.`students` (`student_id`)
    ON DELETE CASCADE,
  CONSTRAINT `registrations_ibfk_2`
    FOREIGN KEY (`event_id`)
    REFERENCES `campushub`.`events` (`event_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `campushub`.`student_clubs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`student_clubs` (
  `student_id` INT NOT NULL,
  `club_id` INT NOT NULL,
  `joined_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`, `club_id`),
  INDEX `club_id` (`club_id` ASC) VISIBLE,
  CONSTRAINT `student_clubs_ibfk_1`
    FOREIGN KEY (`student_id`)
    REFERENCES `campushub`.`students` (`student_id`)
    ON DELETE CASCADE,
  CONSTRAINT `student_clubs_ibfk_2`
    FOREIGN KEY (`club_id`)
    REFERENCES `campushub`.`clubs` (`club_id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
