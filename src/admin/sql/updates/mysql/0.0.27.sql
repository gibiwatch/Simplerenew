ALTER TABLE `#__simplerenew_push_log`
ADD COLUMN `response`  varchar(255) NOT NULL DEFAULT '' AFTER `handler`;
