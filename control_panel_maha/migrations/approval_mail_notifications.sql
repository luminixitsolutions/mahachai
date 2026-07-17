-- Approval / rejection email notification log
-- Timezone: Asia/Kolkata (application layer)
-- Safe to re-run: CREATE IF NOT EXISTS

CREATE TABLE IF NOT EXISTS `tbl_approval_mail_log` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `request_id` int UNSIGNED NOT NULL,
  `stage` varchar(64) NOT NULL DEFAULT '',
  `decision` varchar(32) NOT NULL DEFAULT '',
  `actor_user_id` int UNSIGNED NOT NULL DEFAULT 0,
  `dedupe_key` varchar(64) NOT NULL,
  `to_email` varchar(255) NOT NULL DEFAULT '',
  `cc_emails` text,
  `subject` varchar(500) NOT NULL DEFAULT '',
  `body_preview` text,
  `status` enum('pending','sent','failed','skipped') NOT NULL DEFAULT 'pending',
  `error_message` text,
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_approval_mail_dedupe` (`dedupe_key`),
  KEY `idx_aml_module_request` (`module`, `request_id`),
  KEY `idx_aml_status` (`status`),
  KEY `idx_aml_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_approval_desktop_notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `module` varchar(64) NOT NULL,
  `request_id` int UNSIGNED NOT NULL,
  `stage` varchar(64) NOT NULL DEFAULT '',
  `decision` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `view_url` varchar(1000) NOT NULL DEFAULT '',
  `dedupe_key` varchar(64) NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_approval_desktop_dedupe` (`dedupe_key`),
  KEY `idx_adn_user_delivery` (`user_id`, `delivered_at`),
  KEY `idx_adn_module_request` (`module`, `request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
