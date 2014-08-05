ALTER TABLE `#__simplerenew_plans`
DROP COLUMN `description`,
ADD COLUMN `ordering`  int NOT NULL AFTER `published`;

