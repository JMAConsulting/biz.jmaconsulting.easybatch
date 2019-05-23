--
-- Table structure for table `civicrm_easybatch_entity`
--

CREATE TABLE  IF NOT EXISTS `civicrm_easybatch_entity` (
  `id` int unsigned NOT NULL AUTO_INCREMENT  ,
  `batch_id` int unsigned NOT NULL   COMMENT 'FK to Batch ID',
  `contact_id` int unsigned  COMMENT 'FA organization id',
  `payment_processor_id` int unsigned    COMMENT 'FK payment processor id',
  `is_automatic` tinyint NOT NULL  DEFAULT 0,
  `batch_date` datetime DEFAULT NULL,
  `card_type_id` int unsigned  COMMENT 'Card Type',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_easybatch_entity_batch_id FOREIGN KEY (`batch_id`)
    REFERENCES `civicrm_batch`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civicrm_easybatch_entity_contact_id FOREIGN KEY (`contact_id`)
    REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civicrm_easybatch_entity_payment_processor_id FOREIGN KEY (`payment_processor_id`)
    REFERENCES `civicrm_payment_processor`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
