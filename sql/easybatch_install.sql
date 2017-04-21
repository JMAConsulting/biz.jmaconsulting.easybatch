--
-- Table structure for table `civicrm_easybatch_entity`
--

CREATE TABLE IF NOT EXISTS `civicrm_easybatch_entity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `civicrm_easybatch_entity` ADD CONSTRAINT `civicrm_batch_id_contact_id` UNIQUE (`batch_id`, `contact_id`);
