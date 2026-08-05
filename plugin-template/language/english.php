<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Foo Bar Plugin 0.0                                                        |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) yyyy by the following authors:                              |
// |                                                                           |
// | Authors: author name goes here                                            |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
 * @package FooBar
 *
 * NOTE: This file must be saved in UTF-8 encoding without a BOM (Byte Order Mark).
 */

/**
 * Import Geeklog plugin messages for reuse
 *
 * @global array $LANG32
 */
global $LANG32;

// +---------------------------------------------------------------------------+
// | Array Format:                                                             |
// | $LANGXX[YY]:  $LANG - variable name                                       |
// |               XX    - specific array name                                 |
// |               YY    - phrase id or number                                 |
// +---------------------------------------------------------------------------+

$LANG_FOOBAR_1 = array(
    'plugin_name' => 'Foo Bar',
    'hello' => 'Hello, world!' // this is an example only - feel free to remove
);

// Messages for the plugin upgrade
$PLG_foobar_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

{optional:use_config_ui}
// Localization of the Admin Configuration UI
$LANG_configsections['foobar'] = array(
    'label' => 'Foo Bar',
    'title' => 'Foo Bar Configuration'
);

$LANG_confignames['foobar'] = array(
    'samplesetting1' => 'Sample Setting #1',
    'samplesetting2' => 'Sample Setting #2',
);

$LANG_configsubgroups['foobar'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_tab['foobar'] = array(
    'tab_main' => 'Foo Bar Main Settings'
);

$LANG_fs['foobar'] = array(
    'fs_main' => 'Foo Bar Main Settings'
);

$LANG_configselects['foobar'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => true, 'False' => false)
);
{/optional:use_config_ui}
?>
