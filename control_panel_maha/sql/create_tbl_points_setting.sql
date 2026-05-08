-- Points reward rules per franchise (business partner) and product type.
-- Run once if `tbl_points_setting` does not exist.

CREATE TABLE IF NOT EXISTS `tbl_points_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `points` varchar(50) DEFAULT NULL,
  `rs` varchar(50) DEFAULT NULL,
  `minorder` varchar(50) DEFAULT NULL,
  `frid` int NOT NULL COMMENT 'Business partner user id (tbl_users)',
  `prodtype` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_frid_prodtype` (`frid`,`prodtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
