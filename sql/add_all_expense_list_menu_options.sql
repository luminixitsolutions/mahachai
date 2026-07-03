-- All Expense Lists menu options for add-employee.php / All Expenses sidebar (run once).
INSERT INTO `tbl_option_cp` (`id`, `Name`) VALUES
(175, 'All Leave Requests'),
(176, 'All Advance Request'),
(177, 'All Resign Request'),
(178, 'All Hiring Request')
ON DUPLICATE KEY UPDATE `Name` = VALUES(`Name`);
