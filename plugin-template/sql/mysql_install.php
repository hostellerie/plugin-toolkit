<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
// |                                                                           |
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) yyyy by the following authors:                              |
// |                                                                           |
// | Authors: author name goes here                                            |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['foobar']} (
  fbid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL DEFAULT '',
  body TEXT NOT NULL,
  owner_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 1,
  group_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 1,
  perm_owner TINYINT(1) UNSIGNED NOT NULL DEFAULT 3,
  perm_group TINYINT(1) UNSIGNED NOT NULL DEFAULT 2,
  perm_members TINYINT(1) UNSIGNED NOT NULL DEFAULT 2,
  perm_anon TINYINT(1) UNSIGNED NOT NULL DEFAULT 2,
  PRIMARY KEY (fbid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

?>
