<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | mssql_install.php                                                         |
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
CREATE TABLE [dbo].[{$_TABLES['foobar_table']}] (
    [id] INT IDENTITY(1,1) NOT NULL,
    [title] VARCHAR(255) NOT NULL,
    [body] TEXT NULL,
    [owner_id] NUMERIC(8,0) NOT NULL,
    [group_id] NUMERIC(8,0) NOT NULL,
    [perm_owner] TINYINT NOT NULL,
    [perm_group] TINYINT NOT NULL,
    [perm_members] TINYINT NOT NULL,
    [perm_anon] TINYINT NOT NULL
) ON [PRIMARY]
";

$_SQL[] = "ALTER TABLE [dbo].[{$_TABLES['foobar_table']}] ADD
    CONSTRAINT [PK_{$_TABLES['foobar_table']}] PRIMARY KEY CLUSTERED
    (
        [id]
    )  ON [PRIMARY]
";

?>
