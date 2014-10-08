ALTER TABLE `#__simplerenew_plans`
ADD COLUMN `currency`  char(3) NOT NULL AFTER `trial_unit`;
