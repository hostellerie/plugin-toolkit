<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Plugin Toolkit: Plugin Generator 0.3.0                            |
// +---------------------------------------------------------------------------+
// | plgen.php                                                                 |
// |                                                                           |
// | Creates a plugin template and an optional versioned ZIP archive           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011-2026 by the following authors:                         |
// |                                                                           |
// | Authors: Dirk Haun              dirk AT haun-online DOT de                |
// |          Rouslan Placella       rouslan AT placella DOT com               |
// |          Ben                    hostellerie.org AT gmail DOT com           |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the              |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.            |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
 * @package PluginGenerator
 * @version 0.3.0
 */

define('VERSION', '0.3.0');
define('TEMPLATE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'plugin-template');
define('OUTPUT_PATH', getcwd());

error_reporting(E_ALL);

$pluginData = array(
    'pi_name'         => 'foobar',
    'pi_display_name' => 'Foo Bar',
    'pi_version'      => '1.0',
    'pi_gl_version'   => '2.2.2',
    'pi_homepage'     => 'https://www.example.com/',
    'author'          => 'John Doe',
    'email'           => 'john@example.com',
    'use_sql'         => true,
    'use_config_ui'   => true,
    'create_zip'      => true,
);

/**
 * Activate or remove {optional:...} sections.
 *
 * Optional tags must appear alone on their line. Nested optional sections are
 * supported and malformed structures stop generation with a clear error.
 *
 * @param string $content File content to patch
 * @param array  $plgdata Plugin data
 * @return string
 */
function optionalSections($content, $plgdata)
{
    $lines = preg_split('/\R/', $content);
    if ($lines === false) {
        throw new RuntimeException('Unable to split template content into lines.');
    }

    $newLines = array();
    $stack = array();
    $skip = false;

    foreach ($lines as $lineNumber => $line) {
        if (preg_match('/^\s*\{optional:([a-zA-Z0-9_]+)\}\s*$/', $line, $matches)) {
            $tag = $matches[1];
            $stack[] = array(
                'tag' => $tag,
                'parent_skip' => $skip,
                'condition' => !empty($plgdata[$tag]),
                'else_seen' => false,
            );
            $skip = $skip || empty($plgdata[$tag]);
            continue;
        }

        if (preg_match('/^\s*\{!optional:([a-zA-Z0-9_]+)\}\s*$/', $line, $matches)) {
            if (empty($stack)) {
                throw new RuntimeException(
                    'Unexpected optional else tag on template line ' . ($lineNumber + 1) . '.'
                );
            }

            $index = count($stack) - 1;
            if ($stack[$index]['tag'] !== $matches[1]) {
                throw new RuntimeException(
                    'Mismatched optional else tag on template line ' . ($lineNumber + 1) . '.'
                );
            }
            if ($stack[$index]['else_seen']) {
                throw new RuntimeException(
                    'Duplicate optional else tag on template line ' . ($lineNumber + 1) . '.'
                );
            }

            $stack[$index]['else_seen'] = true;
            $skip = $stack[$index]['parent_skip'] || $stack[$index]['condition'];
            continue;
        }

        if (preg_match('/^\s*\{\/optional:([a-zA-Z0-9_]+)\}\s*$/', $line, $matches)) {
            if (empty($stack)) {
                throw new RuntimeException(
                    'Unexpected optional closing tag on template line ' . ($lineNumber + 1) . '.'
                );
            }

            $section = array_pop($stack);
            if ($section['tag'] !== $matches[1]) {
                throw new RuntimeException(
                    'Mismatched optional closing tag on template line ' . ($lineNumber + 1) . '.'
                );
            }

            $skip = $section['parent_skip'];
            continue;
        }

        if (!$skip) {
            $newLines[] = $line;
        }
    }

    if (!empty($stack)) {
        $section = array_pop($stack);
        throw new RuntimeException(
            "Unclosed optional section '{$section['tag']}' in template."
        );
    }

    return implode(PHP_EOL, $newLines);
}

/**
 * Patch file content with plugin data.
 *
 * This intentionally preserves the original toolkit behavior: file headers
 * use pi_display_name, while technical identifiers use pi_name.
 *
 * @param string $content File content to patch
 * @param array  $plgdata Plugin data
 * @return string
 */
function patch($content, $plgdata)
{
    $headers = createHeaderTemplates();

    foreach ($headers as $name => $comment) {
        $newComment = '';

        switch ($name) {
        case 'authors':
            if (empty($plgdata['email'])) {
                $newComment = formattedComment('Authors: ' . $plgdata['author']);
            } else {
                $newComment = formattedComment(
                    'Authors: ' . $plgdata['author'] . ' - ' . obfuscateEmail($plgdata['email'])
                );
            }
            break;

        case 'copyright':
            $newComment = formattedComment(
                'Copyright (C) ' . date('Y') . ' by the following authors:'
            );
            break;

        case 'pi_name':
            $newComment = formattedComment(
                $plgdata['pi_display_name'] . ' Plugin ' . $plgdata['pi_version']
            );
            break;
        }

        if ($newComment !== '') {
            $content = str_replace($comment, $newComment, $content);
        }
    }

    $idfield = substr($plgdata['pi_name'], 0, 1) . 'id';
    $className = preg_replace('/[^a-zA-Z0-9\-_]/', '', $plgdata['pi_display_name']);

    if ($className === null) {
        throw new RuntimeException('Unable to generate the plugin class name.');
    }

    $content = str_replace(
        array('foobar', 'Foo Bar', 'FooBar', 'FOOBAR', 'fbid'),
        array(
            $plgdata['pi_name'],
            $plgdata['pi_display_name'],
            $className,
            strtoupper($plgdata['pi_name']),
            $idfield,
        ),
        $content
    );

    $content = str_replace('0.0.0', $plgdata['pi_version'], $content);
    $content = str_replace('http://www.example.com/', $plgdata['pi_homepage'], $content);
    $content = str_replace('https://www.example.com/', $plgdata['pi_homepage'], $content);
    $content = str_replace('2.2.2', $plgdata['pi_gl_version'], $content);

    return $content;
}

/**
 * Create list of strings in template headers that need replacement.
 *
 * @return array
 */
function createHeaderTemplates()
{
    return array(
        'pi_name' => formattedComment('Foo Bar Plugin 0.0'),
        'copyright' => formattedComment('Copyright (C) yyyy by the following authors:'),
        'authors' => formattedComment('Authors: author name goes here'),
    );
}

/**
 * Format a copyright-header comment to 80 characters.
 *
 * @param string $text Text to format
 * @return string
 */
function formattedComment($text)
{
    if (strlen($text) > 73) {
        $text = substr($text, 0, 73);
    }

    return sprintf("// | %-73s |\n", $text);
}

/**
 * Make an email address less obvious to simple harvesting bots.
 *
 * @param string $email Email address
 * @return string
 */
function obfuscateEmail($email)
{
    return str_replace(array('@', '.'), array(' AT ', ' DOT '), $email);
}

/**
 * Read and trim one line from a stream.
 *
 * @param resource $fp File handle
 * @return string
 */
function readln($fp)
{
    $value = fgets($fp);

    if ($value === false) {
        throw new RuntimeException('Error reading from standard input.');
    }

    return trim($value);
}

/**
 * Ask the user for a value and apply the displayed default when Enter is used.
 *
 * @param resource $fp           File handle
 * @param string   $desc1        Main prompt
 * @param string   $desc2        Help text
 * @param string   $defaultValue Default value
 * @return string
 */
function getValue($fp, $desc1, $desc2, $defaultValue)
{
    echo "\n";
    echo $desc1 . "\n";
    echo '(' . $desc2 . ') [' . $defaultValue . '] ';

    // Ensure the prompt is visible before waiting for input, especially on Windows.
    if (defined('STDOUT')) {
        fflush(STDOUT);
    }

    $value = readln($fp);

    return ($value === '') ? $defaultValue : $value;
}

/**
 * Ask a strict yes/no question.
 *
 * @param resource $fp          File handle
 * @param string   $question    Main prompt
 * @param string   $description Help text
 * @param bool     $default     Default answer
 * @return bool
 */
function getBooleanValue($fp, $question, $description, $default = true)
{
    $defaultLabel = $default ? 'yes' : 'no';

    while (true) {
        $value = strtolower(
            getValue($fp, $question, $description . ' - yes or no', $defaultLabel)
        );

        if (in_array($value, array('yes', 'y'), true)) {
            return true;
        }

        if (in_array($value, array('no', 'n'), true)) {
            return false;
        }

        echo "Please enter yes, y, no or n.\n";
    }
}

/**
 * Validate the internal plugin name.
 *
 * @param string $name Internal name
 * @return string
 */
function validatePluginName($name)
{
    $name = strtolower(trim($name));

    if (!preg_match('/^[a-z][a-z0-9_]*$/', $name)) {
        throw new InvalidArgumentException(
            'Invalid plugin name. Use lowercase letters, numbers and underscores only, '
            . 'and start with a letter.'
        );
    }

    return $name;
}

/**
 * Validate a plugin or Geeklog version number.
 *
 * @param string $version Version string
 * @param string $label   Human-readable label
 * @return string
 */
function validateVersion($version, $label)
{
    $version = trim($version);

    if (!preg_match('/^\d+\.\d+(?:\.\d+)?(?:[-+][0-9A-Za-z.-]+)?$/', $version)) {
        throw new InvalidArgumentException(
            "Invalid {$label} version '{$version}'. Expected x.y, x.y.z or a valid suffix."
        );
    }

    return $version;
}

/**
 * Validate a URL.
 *
 * @param string $url URL
 * @return string
 */
function validateUrl($url)
{
    $url = trim($url);

    if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL) === false) {
        throw new InvalidArgumentException("Invalid plugin homepage URL '{$url}'.");
    }

    return $url;
}

/**
 * Validate an optional email address.
 *
 * @param string $email Email address
 * @return string
 */
function validateEmail($email)
{
    $email = trim($email);

    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException("Invalid email address '{$email}'.");
    }

    return $email;
}

/**
 * Convert a template-relative path to the current platform format.
 *
 * @param string $path Relative path
 * @return string
 */
function normalizeRelativePath($path)
{
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = explode(DIRECTORY_SEPARATOR, $path);

    foreach ($parts as $part) {
        if ($part === '..') {
            throw new InvalidArgumentException('Parent-directory references are not allowed.');
        }
    }

    return ltrim($path, DIRECTORY_SEPARATOR);
}

/**
 * Return an absolute template path.
 *
 * @param string $filename Relative template file
 * @return string
 */
function getTemplatePath($filename)
{
    return TEMPLATE_PATH . DIRECTORY_SEPARATOR . normalizeRelativePath($filename);
}

/**
 * Return an absolute path inside the generation directory.
 *
 * @param string $generationRoot Temporary generation root
 * @param string $relativePath   Relative path
 * @return string
 */
function getGenerationPath($generationRoot, $relativePath = '')
{
    if ($relativePath === '') {
        return $generationRoot;
    }

    return $generationRoot . DIRECTORY_SEPARATOR . normalizeRelativePath($relativePath);
}

/**
 * Create a directory without overwriting an existing path.
 *
 * @param string $path Absolute directory path
 * @return void
 */
function createDirectory($path)
{
    if (file_exists($path)) {
        throw new RuntimeException("Path already exists: '{$path}'.");
    }

    if (!mkdir($path, 0755, true) && !is_dir($path)) {
        throw new RuntimeException("Failed to create directory '{$path}'.");
    }
}

/**
 * Read a template file.
 *
 * @param string $filename Relative template file
 * @return string
 */
function readTemplate($filename)
{
    $templatePath = getTemplatePath($filename);

    if (!is_file($templatePath) || !is_readable($templatePath)) {
        throw new RuntimeException("Template '{$filename}' is missing or unreadable.");
    }

    $content = file_get_contents($templatePath);

    if ($content === false) {
        throw new RuntimeException("Failed to read template '{$filename}'.");
    }

    return $content;
}

/**
 * Write a generated file atomically.
 *
 * @param string $filename       Relative output filename
 * @param string $content        File content
 * @param string $generationRoot Temporary generation root
 * @return void
 */
function writePluginFile($filename, $content, $generationRoot)
{
    $outputFile = getGenerationPath($generationRoot, $filename);
    $outputDirectory = dirname($outputFile);

    if (!is_dir($outputDirectory)) {
        throw new RuntimeException("Output directory does not exist: '{$outputDirectory}'.");
    }

    $temporaryFile = $outputFile . '.tmp';
    $written = file_put_contents($temporaryFile, $content, LOCK_EX);

    if ($written === false || $written !== strlen($content)) {
        @unlink($temporaryFile);
        throw new RuntimeException("Error writing '{$filename}'.");
    }

    if (!rename($temporaryFile, $outputFile)) {
        @unlink($temporaryFile);
        throw new RuntimeException("Unable to finalize '{$filename}'.");
    }
}

/**
 * Generate one plugin file from its template.
 *
 * @param string $filename       Relative filename
 * @param array  $plgdata        Plugin data
 * @param string $generationRoot Temporary generation root
 * @return void
 */
function generatePluginFile($filename, $plgdata, $generationRoot)
{
    $content = readTemplate($filename);
    $content = patch($content, $plgdata);
    $content = optionalSections($content, $plgdata);
    $content = preg_replace('/\?>\s*$/', '', $content);

    if ($content === null) {
        throw new RuntimeException("Failed to process template '{$filename}'.");
    }

    writePluginFile($filename, $content, $generationRoot);
}

/**
 * Remove a directory tree created during the current generation.
 *
 * @param string $path Directory path
 * @return void
 */
function removeDirectory($path)
{
    if (!is_dir($path)) {
        return;
    }

    $items = scandir($path);
    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $itemPath = $path . DIRECTORY_SEPARATOR . $item;

        if (is_dir($itemPath) && !is_link($itemPath)) {
            removeDirectory($itemPath);
        } else {
            @unlink($itemPath);
        }
    }

    @rmdir($path);
}

/**
 * Copy the default plugin icon.
 *
 * @param array  $plgdata        Plugin data
 * @param string $generationRoot Temporary generation root
 * @return void
 */
function copyPluginIcon($plgdata, $generationRoot)
{
    $sourceIcon = getTemplatePath('admin/images/foobar.png');
    $destinationIcon = getGenerationPath(
        $generationRoot,
        'admin/images/' . $plgdata['pi_name'] . '.png'
    );

    if (!is_file($sourceIcon) || !is_readable($sourceIcon)) {
        throw new RuntimeException("Default plugin icon is missing or unreadable: '{$sourceIcon}'.");
    }

    if (!copy($sourceIcon, $destinationIcon)) {
        throw new RuntimeException('Failed to copy the default plugin icon.');
    }
}

/**
 * Add a directory tree to an open ZIP archive.
 *
 * @param ZipArchive $zip          Open archive
 * @param string     $sourcePath   Absolute directory path
 * @param string     $archiveRoot  Root directory name inside archive
 * @return void
 */
function addDirectoryToZip($zip, $sourcePath, $archiveRoot)
{
    $sourcePath = rtrim($sourcePath, DIRECTORY_SEPARATOR);
    $archiveRoot = trim(str_replace('\\', '/', $archiveRoot), '/');

    if (!$zip->addEmptyDir($archiveRoot)) {
        throw new RuntimeException("Unable to add '{$archiveRoot}' to ZIP archive.");
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourcePath, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $absolutePath = $item->getPathname();
        $relativePath = substr($absolutePath, strlen($sourcePath) + 1);
        $archivePath = $archiveRoot . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        if ($item->isDir()) {
            if (!$zip->addEmptyDir($archivePath)) {
                throw new RuntimeException("Unable to add directory '{$archivePath}' to ZIP archive.");
            }
        } elseif (!$zip->addFile($absolutePath, $archivePath)) {
            throw new RuntimeException("Unable to add file '{$archivePath}' to ZIP archive.");
        }
    }
}

/**
 * Create a versioned ZIP archive when the ZipArchive extension is available.
 *
 * Archive format: plugin-version-geeklog-version.zip
 *
 * @param array  $plgdata         Plugin data
 * @param string $pluginDirectory Final plugin directory
 * @return string|null Archive path, or null when ZipArchive is unavailable
 */
function createPluginArchive($plgdata, $pluginDirectory)
{
    $archiveName = sprintf(
        '%s-%s-%s.zip',
        $plgdata['pi_name'],
        $plgdata['pi_version'],
        $plgdata['pi_gl_version']
    );
    $archivePath = OUTPUT_PATH . DIRECTORY_SEPARATOR . $archiveName;

    if (file_exists($archivePath)) {
        throw new RuntimeException(
            "Archive '{$archiveName}' already exists. Remove or rename it before generating again."
        );
    }

    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        $result = $zip->open($archivePath, ZipArchive::CREATE | ZipArchive::EXCL);

        if ($result !== true) {
            throw new RuntimeException(
                "Unable to create ZIP archive '{$archiveName}' (error {$result})."
            );
        }

        try {
            addDirectoryToZip($zip, $pluginDirectory, $plgdata['pi_name']);
        } catch (Throwable $exception) {
            $zip->close();
            @unlink($archivePath);
            throw $exception;
        }

        if (!$zip->close()) {
            @unlink($archivePath);
            throw new RuntimeException("Unable to finalize ZIP archive '{$archiveName}'.");
        }

        return $archivePath;
    }

    if (class_exists('PharData')) {
        try {
            $archive = new PharData($archivePath);
            $archive->addEmptyDir($plgdata['pi_name']);

            $sourcePath = rtrim($pluginDirectory, DIRECTORY_SEPARATOR);
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourcePath, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $absolutePath = $item->getPathname();
                $relativePath = substr($absolutePath, strlen($sourcePath) + 1);
                $archivePathName = $plgdata['pi_name']
                    . '/'
                    . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

                if ($item->isDir()) {
                    $archive->addEmptyDir($archivePathName);
                } else {
                    $archive->addFile($absolutePath, $archivePathName);
                }
            }

            unset($archive);
        } catch (Throwable $exception) {
            @unlink($archivePath);
            throw new RuntimeException(
                "Unable to create ZIP archive '{$archiveName}' with PharData: "
                . $exception->getMessage(),
                0,
                $exception
            );
        }

        return $archivePath;
    }

    return null;
}

/**
 * Display the chosen configuration.
 *
 * @param array $plgdata Plugin data
 * @return void
 */
function displaySummary($plgdata)
{
    echo "\nPlugin configuration\n";
    echo "--------------------\n";
    echo 'Internal name:   ' . $plgdata['pi_name'] . "\n";
    echo 'Display name:    ' . $plgdata['pi_display_name'] . "\n";
    echo 'Plugin version:  ' . $plgdata['pi_version'] . "\n";
    echo 'Geeklog version: ' . $plgdata['pi_gl_version'] . "\n";
    echo 'Author:          ' . $plgdata['author'] . "\n";
    echo 'Email:           ' . ($plgdata['email'] !== '' ? $plgdata['email'] : '(none)') . "\n";
    echo 'Homepage:        ' . ($plgdata['pi_homepage'] !== '' ? $plgdata['pi_homepage'] : '(none)') . "\n";
    echo 'SQL files:       ' . ($plgdata['use_sql'] ? 'yes' : 'no') . "\n";
    echo 'Configuration:   ' . ($plgdata['use_config_ui'] ? 'yes' : 'no') . "\n";
    echo 'ZIP archive:     ' . ($plgdata['create_zip'] ? 'yes' : 'no') . "\n";
    echo 'Output folder:   ' . OUTPUT_PATH . "\n";
}

// MAIN

echo "\nGeeklog Plugin Toolkit: Plugin Generator " . VERSION . "\n";

$stdin = fopen('php://stdin', 'r');
if ($stdin === false) {
    fwrite(STDERR, "\nUnable to open standard input.\n");
    exit(1);
}

$temporaryDirectory = null;
$finalPluginDirectory = null;

try {
    $pluginData['pi_name'] = validatePluginName(
        getValue(
            $stdin,
            'Internal name of your plugin?',
            'lowercase letters, numbers and underscores only',
            $pluginData['pi_name']
        )
    );

    $defaultDisplayName = ucfirst($pluginData['pi_name']);
    $pluginData['pi_display_name'] = trim(
        getValue(
            $stdin,
            'Display name of your plugin?',
            'used in menu entries and generated file headers',
            $defaultDisplayName
        )
    );

    if ($pluginData['pi_display_name'] === '') {
        throw new InvalidArgumentException('The display name cannot be empty.');
    }

    $pluginData['pi_version'] = validateVersion(
        getValue(
            $stdin,
            'Version number of your plugin?',
            'typically x.y or x.y.z',
            $pluginData['pi_version']
        ),
        'plugin'
    );

    $pluginData['author'] = trim(
        getValue(
            $stdin,
            'Your name?',
            'for the copyright information',
            $pluginData['author']
        )
    );

    if ($pluginData['author'] === '') {
        throw new InvalidArgumentException('The author name cannot be empty.');
    }

    $pluginData['email'] = validateEmail(
        getValue(
            $stdin,
            'Your email address?',
            'optional, will be obfuscated in generated headers',
            ''
        )
    );

    $pluginData['pi_homepage'] = validateUrl(
        getValue(
            $stdin,
            'Plugin homepage?',
            'for example https://example.com/',
            $pluginData['pi_homepage']
        )
    );

    $pluginData['pi_gl_version'] = validateVersion(
        getValue(
            $stdin,
            'Minimum Geeklog version required by your plugin?',
            'for example 2.2.2',
            $pluginData['pi_gl_version']
        ),
        'Geeklog'
    );

    $pluginData['use_sql'] = getBooleanValue(
        $stdin,
        'Create SQL files?',
        'needed if your plugin stores data in the database',
        true
    );

    $pluginData['use_config_ui'] = getBooleanValue(
        $stdin,
        'Create sample entries for the Configuration UI?',
        'needed if the plugin uses Geeklog configuration',
        true
    );

    $pluginData['create_zip'] = getBooleanValue(
        $stdin,
        'Create a versioned ZIP archive?',
        'named plugin-version-geeklogversion.zip',
        true
    );

    displaySummary($pluginData);

    if (!getBooleanValue($stdin, 'Generate this plugin?', 'review the configuration above', true)) {
        echo "\nGeneration cancelled.\n";
        fclose($stdin);
        exit(0);
    }

    $finalPluginDirectory = OUTPUT_PATH . DIRECTORY_SEPARATOR . $pluginData['pi_name'];

    if (file_exists($finalPluginDirectory)) {
        throw new RuntimeException(
            "Plugin directory '{$finalPluginDirectory}' already exists. Generation aborted to prevent data loss."
        );
    }

    $temporaryDirectory = OUTPUT_PATH
        . DIRECTORY_SEPARATOR
        . '.' . $pluginData['pi_name'] . '.tmp-' . bin2hex(random_bytes(6));

    createDirectory($temporaryDirectory);

    $directories = array(
        'admin',
        'admin/images',
        'language',
        'public_html',
    );

    if ($pluginData['use_sql']) {
        $directories[] = 'sql';
    }

    foreach ($directories as $directory) {
        createDirectory(getGenerationPath($temporaryDirectory, $directory));
    }

    $files = array(
        'autoinstall.php',
        'functions.inc',
        'language/english.php',
        'public_html/index.php',
        'admin/index.php',
    );

    if ($pluginData['use_sql']) {
        $files[] = 'sql/mysql_install.php';
        $files[] = 'sql/mssql_install.php';
    }

    if ($pluginData['use_config_ui']) {
        $files[] = 'install_defaults.php';
    }

    foreach ($files as $file) {
        generatePluginFile($file, $pluginData, $temporaryDirectory);
    }

    copyPluginIcon($pluginData, $temporaryDirectory);

    if (!rename($temporaryDirectory, $finalPluginDirectory)) {
        throw new RuntimeException(
            "Unable to finalize plugin directory '{$finalPluginDirectory}'."
        );
    }

    $temporaryDirectory = null;
    $archivePath = null;

    if ($pluginData['create_zip']) {
        try {
            $archivePath = createPluginArchive($pluginData, $finalPluginDirectory);
        } catch (Throwable $archiveException) {
            fwrite(
                STDERR,
                "\nPlugin generated, but ZIP creation failed: "
                . $archiveException->getMessage()
                . "\n"
            );
        }
    }

    echo "\nGeneration completed successfully.\n";
    echo 'Plugin directory: ' . $finalPluginDirectory . "\n";

    if ($pluginData['create_zip']) {
        if ($archivePath !== null) {
            echo 'ZIP archive:     ' . $archivePath . "\n";
        } elseif (!class_exists('ZipArchive') && !class_exists('PharData')) {
            echo "ZIP archive:     not created (no ZIP-capable PHP extension is available)\n";
        }
    }

    echo "\n";
} catch (Throwable $exception) {
    if ($temporaryDirectory !== null && is_dir($temporaryDirectory)) {
        removeDirectory($temporaryDirectory);
    }

    fwrite(STDERR, "\nGeneration failed: " . $exception->getMessage() . "\n\n");
    fclose($stdin);
    exit(1);
}

fclose($stdin);
