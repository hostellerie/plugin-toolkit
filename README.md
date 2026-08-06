# Geeklog Plugin Toolkit 0.3.0

Plugin development toolkit updated for **Geeklog 2.2.2** and **PHP 8.1 or later**.

The Geeklog Plugin Toolkit helps developers create and maintain Geeklog plugins.

Its main component is the Plugin Generator, `plgen.php`, which creates an installable starter plugin structure containing the files required for a new Geeklog plugin.

The generator can also create a versioned ZIP archive ready for testing, distribution, or installation.

## Features

The generated plugin skeleton can include:

* installation, upgrade, and uninstallation support;
* public and administration pages;
* Geeklog autoinstall support;
* optional SQL installation and removal files;
* optional Geeklog Configuration UI defaults;
* language files;
* permission and access-control examples;
* CSRF protection examples;
* modern Geeklog page rendering;
* configurable minimum Geeklog version requirements;
* a default administration icon;
* a versioned ZIP distribution archive.

The generator creates the technical foundation of a plugin. You must then implement its specific features and business logic.

## Requirements

* **Geeklog:** 2.2.2 by default
* **PHP:** 8.1 or later
* **PHP CLI:** required to run the generator
* **Encoding:** UTF-8 without a BOM
* **Database character set:** `utf8mb4` recommended
* **ZIP support:** PHP `ZipArchive` or `PharData` required for archive generation

The minimum required Geeklog version can be changed when running the generator.

ZIP archive generation is optional. If neither `ZipArchive` nor `PharData` is available, the plugin directory is still generated.

## Toolkit Structure

The generator expects the following structure:

```text
plugin-toolkit/
├── plgen.php
└── plugin-template/
    ├── autoinstall.php
    ├── functions.inc
    ├── install_defaults.php
    ├── admin/
    ├── language/
    ├── public_html/
    └── sql/
```

The `plugin-template` directory must remain in the same directory as `plgen.php`.

The generator uses its own location to find the templates, so it can be launched from another working directory.

## Usage

Run the Plugin Generator from the command line:

```bash
cd plugin-toolkit
php plgen.php
```

You can also launch it using its full path:

```bash
php /path/to/plugin-toolkit/plgen.php
```

In that case, the generated plugin and ZIP archive are created in the current working directory.

Follow the on-screen instructions.

Default values are displayed in square brackets:

```text
Internal name of your plugin?
(lowercase letters, numbers and underscores only) [foobar]
```

Press Enter to accept the default value, or enter a different value.

Before generating the files, the script displays a configuration summary and asks for confirmation.

## Generator Questions

The generator asks for:

* the internal plugin name;
* the display name;
* the plugin version;
* the author name;
* an optional email address;
* the plugin homepage;
* the minimum required Geeklog version;
* whether SQL files should be created;
* whether Configuration UI defaults should be created;
* whether a versioned ZIP archive should be created;
* final confirmation before generation.

## Internal Name and Display Name

The internal name is the technical plugin identifier.

Example:

```text
zip
```

It is used for:

* directory names;
* URLs;
* permission names;
* function names;
* configuration identifiers;
* generated filenames.

The internal name must:

* begin with a lowercase letter;
* contain only lowercase letters, numbers, and underscores;
* contain no spaces or hyphens.

Example of a valid internal name:

```text
myplugin
```

Example of another valid internal name:

```text
my_plugin
```

The display name is the human-readable name shown in generated headers and interface labels.

Example:

```text
ZIP Manager
```

The current generator intentionally uses the display name in generated file headers.

## Input Validation

The optimized generator validates important input before creating files.

It checks:

* the internal plugin name;
* the plugin version;
* the minimum Geeklog version;
* the email address, when provided;
* the homepage URL;
* `yes` and `no` answers.

Plugin and Geeklog versions must use one of these formats:

```text
1.0
1.0.0
2.2.2
```

Invalid values stop generation with an explanatory error.

## Generated Files

Depending on the selected options, the generated plugin can contain:

```text
myplugin/
├── autoinstall.php
├── functions.inc
├── install_defaults.php
├── admin/
│   ├── index.php
│   └── images/
│       └── myplugin.png
├── language/
│   └── english.php
├── public_html/
│   └── index.php
└── sql/
    ├── mysql_install.php
    └── mssql_install.php
```

The exact structure depends on the selected options.

### Files Always Generated

The following files are always generated:

```text
autoinstall.php
functions.inc
admin/index.php
admin/images/myplugin.png
language/english.php
public_html/index.php
```

### Optional SQL Files

When SQL support is enabled, the generator also creates:

```text
sql/mysql_install.php
sql/mssql_install.php
```

### Optional Configuration File

When Configuration UI support is enabled, the generator also creates:

```text
install_defaults.php
```

## Output Location

The generated plugin is created in the directory from which the generator is executed.

For example:

```bash
cd /home/user/geeklog-projects
php /home/user/plugin-toolkit/plgen.php
```

The result is created in:

```text
/home/user/geeklog-projects/myplugin/
```

The ZIP archive, when requested, is created in the same output directory.

## ZIP Archive Generation

The generator can automatically create a versioned ZIP archive.

The archive name follows this format:

```text
plugin-version-geeklogversion.zip
```

For example, a plugin configured with:

```text
Internal name: myplugin
Plugin version: 1.0
Geeklog version: 2.2.2
```

produces:

```text
myplugin-1.0-2.2.2.zip
```

The archive contains the plugin directory as its root:

```text
myplugin-1.0-2.2.2.zip
└── myplugin/
    ├── autoinstall.php
    ├── functions.inc
    ├── admin/
    ├── language/
    ├── public_html/
    └── sql/
```

This structure is suitable for Geeklog plugin installation and distribution.

### ZIP Support

The generator first tries to use:

```php
ZipArchive
```

If `ZipArchive` is unavailable, it tries:

```php
PharData
```

If neither is available, the plugin directory is still created, but no archive is produced.

### Existing Archives

The generator does not overwrite an existing ZIP archive.

For example, if this file already exists:

```text
myplugin-1.0-2.2.2.zip
```

ZIP generation stops with an error message.

Remove, rename, or move the existing archive before running the generator again.

## Safe Generation Process

The optimized generator uses a temporary directory during generation.

For example:

```text
.myplugin.tmp-a1b2c3d4e5f6/
```

Files are first generated inside this temporary directory.

When all files have been created successfully, the temporary directory is renamed to the final plugin directory:

```text
myplugin/
```

If an error occurs before completion, the temporary directory is removed automatically.

This prevents incomplete plugin directories from being left behind.

## Existing Plugin Protection

The generator refuses to overwrite an existing plugin directory.

If this directory already exists:

```text
myplugin/
```

generation is stopped.

This protects existing source code from accidental replacement.

Remove, rename, or move the existing directory before generating another plugin with the same internal name.

## Template Processing

The generator uses the templates stored in:

```text
plugin-template/
```

It replaces the default template values with the information entered during generation.

Current template placeholders include variations of:

```text
foobar
Foo Bar
FooBar
FOOBAR
fbid
```

The generator also replaces:

* the plugin version;
* the minimum Geeklog version;
* the homepage URL;
* the author information;
* the copyright year;
* optional SQL sections;
* optional Configuration UI sections.

The existing templates do not need to be modified to use the optimized generator.

## Optional Template Sections

Templates can contain optional sections such as:

```text
{optional:use_sql}
Content included when SQL support is enabled.
{/optional:use_sql}
```

An alternative section can be defined with:

```text
{optional:use_sql}
SQL-enabled content.
{!optional:use_sql}
Content used without SQL support.
{/optional:use_sql}
```

Optional markers must appear alone on their line.

Nested optional sections are supported.

Malformed or unclosed optional sections stop generation with a clear error instead of silently producing invalid files.

## What's New in Version 0.3.0

### Safer File Generation

Plugins are now generated inside a temporary directory.

The final plugin directory is created only after all files have been generated successfully.

Temporary files are removed automatically when generation fails.

### Existing Directory Protection

The generator refuses to overwrite an existing plugin directory.

This prevents accidental loss of previous work.

### Versioned ZIP Archives

The generator can create a ZIP archive using the following filename format:

```text
plugin-version-geeklogversion.zip
```

Example:

```text
myplugin-1.0-2.2.2.zip
```

The archive includes the plugin directory as its root.

### ZIP Extension Fallback

The generator uses `ZipArchive` when available.

If `ZipArchive` is unavailable, it attempts to use `PharData`.

### Robust Template Paths

Templates are located relative to `plgen.php` using `__DIR__`.

The generator no longer depends on the current working directory to find the `plugin-template` directory.

### Configurable Output Location

The plugin is generated in the current working directory.

This allows the toolkit to remain in one location while generating plugins in separate project directories.

### Input Validation

The generator validates:

* internal plugin names;
* plugin versions;
* Geeklog versions;
* email addresses;
* homepage URLs;
* yes/no answers.

### Confirmation Summary

A complete configuration summary is displayed before generation.

The user must confirm the configuration before files are created.

### Improved Console Input

Console output is explicitly flushed after each question.

This prevents prompts from remaining hidden in buffered output on some Windows PHP CLI configurations.

### Improved Optional Sections

Optional template sections now support nesting and stricter syntax checking.

Malformed sections stop generation with a readable error message.

### Atomic File Writing

Generated files are first written to temporary files and then moved into place.

This reduces the risk of incomplete files if writing is interrupted.

### PHP Closing Tag Removal

Closing PHP tags are removed from generated PHP-only files.

This helps prevent accidental whitespace output and HTTP header errors.

## Geeklog 2.2.2 Support

The generator and its templates target Geeklog 2.2.2 by default.

The minimum required Geeklog version can be changed during generation.

Generated plugins should use APIs and structures compatible with their declared minimum Geeklog version.

## PHP 8 Compatibility

Generated code targets PHP 8.1 or later.

The templates avoid known deprecated practices and use syntax compatible with current PHP versions.

The generator itself uses modern PHP features and error handling, including:

```php
Throwable
RuntimeException
random_bytes()
```

## Modern Page Rendering

Generated public and administration pages use:

```php
COM_createHTMLDocument()
COM_output()
```

These functions replace legacy sandwich-layout patterns.

## Updated Autoinstall Template

The generated `autoinstall.php` file uses a function-based structure.

It includes the minimum required Geeklog version through the `pi_gl_version` field.

## Configurable Geeklog Version

The generator prompts for the minimum Geeklog version required by the plugin.

The selected value is inserted dynamically into generated files.

## Updated SQL Templates

Generated SQL templates use modern defaults such as:

```sql
CREATE TABLE IF NOT EXISTS
```

The default storage engine is:

```sql
ENGINE=InnoDB
```

The default character set and collation are:

```sql
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci
```

Primary keys use numeric `AUTO_INCREMENT` fields by default.

Review and adapt the generated schema to the actual data model of your plugin.

## CSRF Protection Examples

Generated administration forms use Geeklog CSRF protection:

```php
SEC_createToken()
SEC_checkToken()
```

The submitted form field uses the Geeklog token-name constant:

```php
CSRF_TOKEN
```

Example:

```php
$display .= '<input type="hidden" name="'
    . CSRF_TOKEN
    . '" value="'
    . $token
    . '">';
```

The submitted token must be checked before creating a new token:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SEC_checkToken();

    // Process the submitted form.
}

$token = SEC_createToken();
```

`SEC_checkToken()` handles invalid or expired tokens through Geeklog's reauthentication process.

All state-changing forms should use Geeklog's CSRF protection functions.

## Updated Language Templates

Generated language files include a reminder that they must be saved as:

```text
UTF-8 without a BOM
```

This allows full Unicode support, including emojis, when the database uses `utf8mb4`.

The templates also use safe fallback patterns such as the PHP null coalescing operator.

## English Code Comments

Inline comments in generated files are standardized in English.

This makes the templates easier to maintain and share with international developers.

## Plugin Development Notes

### Business Logic

The generator creates an installable plugin skeleton.

You must implement the plugin-specific functionality, including:

* functions in `functions.inc`;
* public-facing pages;
* administration actions;
* configuration options;
* database operations;
* search integration;
* menu entries;
* autotags;
* scheduled tasks;
* API integrations.

Only add the components required by your plugin.

### Security

Use the Geeklog security APIs available in the minimum version supported by your plugin.

Protect every state-changing form with:

```php
SEC_createToken()
SEC_checkToken()
```

Use Geeklog permission checks such as:

```php
SEC_hasRights()
```

Never rely only on hidden form fields, page visibility, or administration URLs to restrict access.

Escape dynamic content before inserting it into HTML.

Use the appropriate Geeklog filtering and validation functions for submitted values.

### Database Schema

Review and adapt all generated SQL before using it in production.

Check:

* field types;
* indexes;
* primary keys;
* foreign-key requirements;
* default values;
* character sets;
* collations;
* upgrade scripts;
* uninstall behavior.

### Language File Encoding

Language files must be saved as UTF-8 without a BOM.

A BOM can cause output problems, HTTP header errors, or unexpected characters before the page content.

### Theme Compatibility

Generated plugins should remain independent of a specific Geeklog theme.

Use Geeklog templates and APIs rather than hard-coded theme paths or theme-specific markup.

## Development Recommendations

Before releasing a generated plugin:

1. Test installation on a clean Geeklog 2.2.2 site.
2. Test installation from the generated ZIP archive.
3. Verify that the archive contains the plugin directory as its root.
4. Test plugin upgrades from every supported previous version.
5. Test uninstallation and data-removal behavior.
6. Test with PHP 8.1 and PHP 8.3.
7. Enable Geeklog debugging and review PHP logs.
8. Verify all permission checks.
9. Verify CSRF protection on every state-changing action.
10. Test with different Geeklog themes.
11. Validate language files as UTF-8 without a BOM.
12. Review SQL compatibility with the target database configuration.
13. Confirm that no temporary generation directory remains.
14. Confirm that the plugin directory and archive names use the expected versions.

## Troubleshooting

### The Generator Cannot Find a Template

Confirm that the directory is named exactly:

```text
plugin-template
```

It must be located in the same directory as `plgen.php`.

### The Plugin Directory Already Exists

The generator does not overwrite existing directories.

Rename, move, or remove the existing plugin directory before running the generator again.

### The ZIP Archive Already Exists

The generator does not overwrite an existing archive.

Rename, move, or remove the existing archive before generating it again.

### No ZIP Archive Is Created

Check whether one of these PHP classes is available:

```bash
php -r "var_dump(class_exists('ZipArchive'));"
php -r "var_dump(class_exists('PharData'));"
```

To inspect loaded PHP modules:

```bash
php -m
```

On some systems, the PHP ZIP extension must be installed or enabled separately.

### A Prompt Does Not Appear on Windows

Use the updated `plgen.php` version, which flushes console output after each prompt.

Also confirm that the script is being executed with PHP CLI:

```bash
php --version
```

### Generation Stops After Invalid Input

Read the displayed error message and restart the generator with a valid value.

The generator intentionally stops instead of silently modifying invalid input.

## Additional Resources

* [Geeklog Plugin Developer Handbook](https://wiki.geeklog.net/index.php/Plugin_Developers_Handbook)
* [Geeklog Plugin API](https://wiki.geeklog.net/index.php/Plugin_API)
* [Geeklog Core on GitHub](https://github.com/Geeklog-Core/geeklog)

## License

The Geeklog Plugin Toolkit is released under the GNU General Public License version 2, or, at your option, any later version.

See the license file included with the toolkit for the complete license terms.

## Authors

* Dirk Haun — `dirk AT haun-online DOT de`
* Rouslan Placella — `rouslan AT placella DOT com`
* Ben — `hostellerie.org AT gmail DOT com`

## Credits

* Euan McKay for the original plugin generator idea.
* Tom Willet and Blaine Lang for the Plugin Developers Handbook and the Universal Plugin.

## Version History

### 0.3.0

* Added safe generation through a temporary directory.
* Added automatic cleanup after failed generation.
* Added protection against overwriting existing plugin directories.
* Added optional versioned ZIP archive generation.
* Added the `plugin-version-geeklogversion.zip` filename format.
* Added `ZipArchive` support with `PharData` fallback.
* Added robust template paths based on `__DIR__`.
* Added configurable output based on the current working directory.
* Added validation for plugin names, versions, emails, URLs, and yes/no answers.
* Added a configuration summary and confirmation step.
* Added explicit console-output flushing for Windows PHP CLI.
* Improved optional template-section parsing.
* Added support for nested optional sections.
* Added atomic file writing.
* Added automatic removal of PHP closing tags from generated PHP files.
* Preserved compatibility with the existing templates.

### 0.2.0

* Updated the generator for Geeklog 2.2.2.
* Updated templates for PHP 8.1 or later.
* Added configurable minimum Geeklog version support.
* Added modern page-rendering examples.
* Updated the autoinstall template.
* Added `utf8mb4` and `InnoDB` SQL defaults.
* Added CSRF protection examples.
* Added UTF-8 encoding guidance for language files.
* Standardized generated code comments in English.

### 0.1.1

* Added optional SQL table creation.
* Added SQL installation support.
* Added function templates for search integration.
* Added autotag function templates.
* Added menu-entry function templates.

### 0.1.0

* Initial release.
