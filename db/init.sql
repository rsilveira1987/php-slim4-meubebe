CREATE DATABASE meubebe;

CONNECT meubebe;

CREATE TABLE `tb_babies` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(36) NOT NULL,
  `name` VARCHAR(1024) NOT NULL,
  `description` VARCHAR(1024),
  `gender` VARCHAR(1) NOT NULL,
  `born_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT c1_gender CHECK (gender = 'F' OR gender = 'M')
);

CREATE TABLE `tb_appointments` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(36) NOT NULL,
  `uuid_owner` VARCHAR(36) NOT NULL,
  `title` VARCHAR(1024) NOT NULL,
  `description` VARCHAR(1024),
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `due_to` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO tb_babies (uuid, name, description, gender, born_at) VALUES ( UUID(), 'Mateus Vieira Cardozo', 'Lorem ipsum dolor sit amet consectetur adipisicing elit.', 'M', '2022-09-05');
INSERT INTO tb_babies (uuid, name, description, gender, born_at) VALUES ( UUID(), 'Outro Bebê Qualquer', 'Cumque ab minima alias dolor deserunt, enim dicta suscipit placeat nesciunt sequi, ducimus error a.', 'F', '2022-01-01');
