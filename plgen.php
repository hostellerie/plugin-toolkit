<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Plugin Toolkit: Plugin Generator 0.2.0                            |
// +---------------------------------------------------------------------------+
// | plgen.php                                                                 |
// |                                                                           |
// | Creates a plugin template                                                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Dirk Haun              dirk AT haun-online DOT de                |
// |          Rouslan Placella       rouslan AT placella DOT com               |
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
 * @package PluginGenerator
 * @version 0.2.0
 */
define('VERSION', '0.2.0');

// this script will run locally, so reporting all errors shouldn't be a
// security issue ...
error_reporting(E_ALL);

// default data
$pluginData = array(
    'pi_name'         => 'foobar',
    'pi_display_name' => 'Foo Bar',
    'pi_version'      => '1.0',
    'pi_gl_version'   => '2.2.2',
    'pi_homepage'     => 'http://www.example.com/',
    'author'          => 'John Doe',
    'email'           => 'john@example.com',
    'use_sql'         => true,
    'use_config_ui'   => true
);

/**
* Activate or remove {optional:...} sections
*
* NOTE: Expects {optional:} tags to be at the start of the line
*
* @param    string  $content    file content to patch
* @param    array   $plgdata    plugin data
* @return   string              patched file content
*
*/
function optionalSections($content, $plgdata)
{
    $newlines = array();
    $lines = explode("\n", $content);

    $skip = false;
    foreach ($lines as $line) {
        if (strpos($line, '{optional:') !== false) { // "if"
            $x = explode(':', $line);
            if (count($x) != 2) {
                die("\nsyntax error in {optional:} tag - aborting\n");
            }
            $tag = trim(str_replace('}', '', $x[1]));

            if (isset($plgdata[$tag]) && $plgdata[$tag]) {
                $skip = false;
            } else {
                $skip = true;
            }
        } elseif (strpos($line, '{!optional:') !== false) { // "else"
            $skip = ! $skip;
        } elseif (strpos($line, '{/optional:') !== false) { // "endif"
            $skip = false;
        } elseif (! $skip) {
            $newlines[] = $line;
        }
    }

    return implode("\n", $newlines);
}

/**
* Patch file content with plugin data
*
* @param    string  $content    file content to patch
* @param    array   $plgdata    plugin data
* @return   string              patched file content
*
*/
function patch($content, $plgdata)
{
    $headers = createHeaderTemplates();
    foreach ($headers as $name => $comment) {
        $newComment = '';

        switch ($name) {
        case 'authors':
            if (empty($plgdata['email'])) {
                $newComment = formattedComment('Authors: '
                                               . $plgdata['author']);
            } else {
                $newComment = formattedComment('Authors: ' . $plgdata['author']
                                . ' - ' . obfuscateEmail($plgdata['email']));
            }
            break;

        case 'copyright':
            // CORRECTIF PHP 8.1+ : Remplacement de strftime par date
            $newComment = formattedComment('Copyright (C) ' . date('Y')
                            . ' by the following authors:');
            break;

        case 'pi_name':
            $newComment = formattedComment($plgdata['pi_display_name']
                            . ' Plugin ' . $plgdata['pi_version']);
            break;

        default:
            break;
        }

        // echo "$name:\n$comment$newComment";

        if (! empty($newComment)) {
            $content = str_replace($comment, $newComment, $content);
        }
    }

    $idfield = substr($plgdata['pi_name'], 0, 1) . 'id';

    $content = str_replace(
        array('foobar', 'Foo Bar', 'FooBar', 'FOOBAR', 'fbid'),
        array($plgdata['pi_name'],
              $plgdata['pi_display_name'],
              preg_replace('/[^a-zA-Z0-9\-_]/', '',
                           $plgdata['pi_display_name']),
              strtoupper($plgdata['pi_name']),
              $idfield),
        $content
    );

    $content = str_replace('0.0.0', $plgdata['pi_version'], $content);
    $content = str_replace('http://www.example.com/', $plgdata['pi_homepage'],
                           $content);

    // replace default Geeklog version in templates
    $content = str_replace('2.2.2', $plgdata['pi_gl_version'], $content);

    return $content;
}

/**
* Create list of strings in template headers that we need to replace
*
* @return   array   list of header comments
*
*/
function createHeaderTemplates()
{
    $headers = array();

    $headers['pi_name']   = formattedComment('Foo Bar Plugin 0.0');
    $headers['copyright'] = formattedComment('Copyright (C) yyyy by the following authors:');
    $headers['authors']   = formattedComment('Authors: author name goes here');

    return $headers;
}

/**
* Format comment for a file's copyright header
*
* @param    string  $text   text to be formatted
* @return   string          formatted text, 80 characters long
*
*/
function formattedComment($text)
{
    if (strlen($text) > 73) {
        $text = substr($text, 0, 73);
    }

    return sprintf("// | %-73s |\n", $text);
}

/**
* Make the email address slightly less obvious
*
* @param    string  $email  email address
* @return   string          obfuscated email address
*
*/
function obfuscateEmail($email)
{
    return str_replace(array('@', '.'), array(' AT ', ' DOT '), $email);
}

/**
* Read a line from a file handle (usually stdin)
*
* NOTE: aborts entire script on error
*
* @param    filehandle  $fp     file handle, e.g. of 'php://stdin'
* @return   string              string read, possibly empty
*
*/
function readln($fp)
{
    $value = fgets($fp);

    if ($value === false) {
        // "this shouldn't happen"
        die("\nError reading from stdin - aborting\n");
    }

    $value = trim($value);

    return $value;
}

/**
* Create plugin directory
*
* NOTE: aborts entire script on error
*
* @param    string  $dirname    subdirectory or empty for main directory
* @param    array   $plgdata    plugin data
* @return   void
*
*/
function createPluginDirectory($dirname, $plgdata)
{
    if (empty ($dirname)) {
        $path = $plgdata['pi_name'];
    } else {
        $path = $plgdata['pi_name'] . '/' . $dirname;
    }

    if (mkdir($path) === false) {
        die("\nFailed to create directory '$path' - aborting\n");
    }
}

/**
* Read content from one of the plugin-template files
*
* NOTE: aborts entire script on error
*
* @param    string  $filename   file name (relative path)
* @return   string              file content
*
*/
function readTemplate($filename)
{
    $content = file_get_contents('plugin-template/' . $filename);
    if (($content === false) || empty($content)) {
        die("\nFailed to read template '$filename' - aborting\n");
    }

    return $content;
}

/**
* Write patched plugin file
*
* NOTE: aborts entire script on error
*
* @param    string  $filename   file name (relative path)
* @param    string  $content    contents of the file
* @param    array   $plgdata    plugin data
* @return   void
*
*/
function writePluginFile($filename, $content, $plgdata)
{
    $outfileName = $plgdata['pi_name'] . '/' . $filename;
    $written = file_put_contents($outfileName, $content);
    if (($written === false) || ($written < strlen($content))) {
        die("\nError writing '$filename' - aborting\n");
    }
}

/**
* Generate plugin file from template
*
* NOTE: aborts entire script on error
*
* @param    string  $filename   file name (relative path)
* @param    array   $plgdata    plugin data
* @return   void
*
*/
function generatePluginFile($filename, $plgdata)
{
    $content = readTemplate($filename);
    $content = patch($content, $plgdata);
    $content = optionalSections($content, $plgdata);
    // Remove closing PHP tag if present to avoid accidental output/BOM issues
    $content = preg_replace('/\?>\s*$/', '', $content);
    writePluginFile($filename, $content, $plgdata);
}

/**
* Ask user for a value, e.g. plugin name
*
* @param    filehandle  $fp             file handle, e.g. of 'php://stdin'
* @param    string      $desc1          first line of description
* @param    string      $desc2          second line of description
* @param    string      $defaultValue   default value, displayed in [...]
* @return   string                      value the user entered
*
*/
function getValue($fp, $desc1, $desc2, $defaultValue)
{
    echo "\n";
    echo $desc1 . "\n";
    echo "($desc2) [$defaultValue] ";

    $value = readln($fp);

    return $value;
}

// MAIN
echo "\nGeeklog Plugin Toolkit: Plugin Generator " . VERSION . "\n";

/**
* Get plugin information from user
*/
$stdin = fopen('php://stdin', 'r');

$name = getValue($stdin, 'Internal name of your plugin?',
                 'used in URLs - no spaces!', $pluginData['pi_name']);
$name = preg_replace('/[^a-zA-Z0-9\-_]/', '', $name);
if (! empty($name)) {
    $pluginData['pi_name'] = $name;
}

$pluginData['pi_display_name'] = ucfirst($pluginData['pi_name']);

$name = getValue($stdin, 'Display name of your plugin?',
                 'used in menu entries', $pluginData['pi_display_name']);
if (! empty($name)) {
    $pluginData['pi_display_name'] = $name;
}

$version = getValue($stdin, 'Version number of your plugin?',
                    'typically x.y or x.y.z', $pluginData['pi_version']);
if (! empty($version)) {
    $pluginData['pi_version'] = $version;
}

$name = getValue($stdin, 'Your name?', 'for the copyright info',
                 $pluginData['author']);
if (! empty($name)) {
    $pluginData['author'] = $name;
}

$email = getValue($stdin, 'Your email address?', 'optional, will be obfuscated',
                  '');
$pluginData['email'] = $email;

$homepage = getValue($stdin, 'Plugin homepage?', 'e.g. for updates',
                     $pluginData['pi_homepage']);
if (! empty($homepage)) {
    $pluginData['pi_homepage'] = $homepage;
}

$glversion = getValue($stdin, 'Minimum Geeklog version required by your plugin?', 'e.g. 2.2.2', $pluginData['pi_gl_version']);
if (! empty($glversion)) {
    $pluginData['pi_gl_version'] = $glversion;
}

$useSql = getValue($stdin, 'Create SQL files?',
                   'needed if your plugin will store data in the database',
                   'yes');
if (empty($useSql)) {
    $useSql = true;
} else {
    $useSql = strtolower($useSql);
    if (($useSql == 'yes') || ($useSql == 'y')) {
        $useSql = true;
    } else {
        $useSql = false;
    }
}
$pluginData['use_sql'] = $useSql;

$useConfigUI = getValue($stdin, 'Create some sample entries for the Configuration UI?',
                   'needed if your plugin will use Geeklog\'s configuration to store settings',
                   'yes');
if (empty($useConfigUI)) {
    $useConfigUI = true;
} else {
    $useConfigUI = strtolower($useConfigUI);
    if (($useConfigUI == 'yes') || ($useConfigUI == 'y')) {
        $useConfigUI = true;
    } else {
        $useConfigUI = false;
    }
}
$pluginData['use_config_ui'] = $useConfigUI;

fclose($stdin);


/**
* create plugin directories
*/
createPluginDirectory('', $pluginData);
createPluginDirectory('admin', $pluginData);
createPluginDirectory('admin/images', $pluginData);
createPluginDirectory('language', $pluginData);
createPluginDirectory('public_html', $pluginData);
if ($pluginData['use_sql']) {
    createPluginDirectory('sql', $pluginData);
}

/**
* create code files
*/
generatePluginFile('autoinstall.php', $pluginData);
generatePluginFile('functions.inc', $pluginData);
generatePluginFile('language/english.php', $pluginData);
generatePluginFile('public_html/index.php', $pluginData);
generatePluginFile('admin/index.php', $pluginData);
if ($pluginData['use_sql']) {
    generatePluginFile('sql/mysql_install.php', $pluginData);
    generatePluginFile('sql/mssql_install.php', $pluginData);
}
if ($pluginData['use_config_ui']) {
    generatePluginFile('install_defaults.php', $pluginData);
}

/**
* copy default plugin icon
*/
$success = copy('plugin-template/admin/images/foobar.png',
                $pluginData['pi_name'] . '/admin/images/'
                    . $pluginData['pi_name'] . '.png');
if ($success === false) {
    die("\nFailed to copy plugin icon - aborting\n");
}


echo "\nAll done! You'll find your plugin in the '{$pluginData['pi_name']}' subdirectory.\n\n";

?>
