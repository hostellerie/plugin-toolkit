# Geeklog Plugin Toolkit 0.2.0

Plugin development toolkit updated for **Geeklog 2.2.2** and **PHP 8.1 or later**.

The Geeklog Plugin Toolkit helps developers create and maintain Geeklog plugins.

Its main component is the Plugin Generator, `plgen.php`, which creates an installable starter plugin structure containing the files and templates required for a new plugin.

## Features

The generated plugin skeleton includes:

* installation, upgrade, and uninstallation support;
* public and administration pages;
* Geeklog autoinstall support;
* SQL installation and removal templates;
* language files;
* permission and access-control examples;
* CSRF protection examples;
* modern Geeklog page rendering;
* configurable minimum Geeklog version requirements.

The generator creates the technical foundation of a plugin. You must then implement its specific features and business logic.

## Requirements

* **Geeklog:** 2.2.2 by default
* **PHP:** 8.1 or later
* **PHP CLI:** required to run the generator
* **Encoding:** UTF-8 without a BOM
* **Database character set:** `utf8mb4` recommended

The minimum required Geeklog version can be changed when running the generator.

## Usage

Run the Plugin Generator from the command line:

```bash
cd plugin-toolkit
php plgen.php
```

Follow the on-screen instructions.

Default values are displayed in square brackets:

```text
Plugin name [example]:
```

Press Enter to accept the default value, or enter a different value.

The generator creates a new directory in the current folder using the plugin name you provide.

For example:

```text
plugin-toolkit/
├── plgen.php
├── templates/
└── myplugin/
```

The generated plugin can then be copied or installed in a Geeklog 2.2.2 development environment.

## Generated Plugin Structure

Depending on the selected options, the generated plugin can include:

```text
myplugin/
├── autoinstall.php
├── functions.inc
├── install_defaults.php
├── README
├── admin/
├── language/
├── public_html/
├── sql/
└── templates/
```

The exact structure may vary according to the options selected during generation.

## What's New in Version 0.2.0

### Geeklog 2.2.2 Support

The generator and its templates now target Geeklog 2.2.2 by default.

Legacy development patterns have been replaced with APIs and structures suitable for current Geeklog plugin development.

### PHP 8 Compatibility

Generated code targets PHP 8.1 or later.

The templates avoid known deprecated practices and include updates for improved compatibility with modern PHP versions.

### Modern Page Rendering

Generated public and administration pages use:

```php
COM_createHTMLDocument()
COM_output()
```

These functions replace legacy sandwich layout patterns.

### Updated Autoinstall Template

The generated `autoinstall.php` file uses a function-based structure and includes the plugin's minimum Geeklog version through the `pi_gl_version` field.

### Configurable Geeklog Version

The generator prompts for the minimum Geeklog version required by the plugin.

The selected value is inserted dynamically into the generated files.

### Updated SQL Templates

Generated SQL templates use modern defaults:

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

Adapt the generated schema to the actual data model of your plugin.

### CSRF Protection Examples

Generated administration forms include examples using:

```php
SEC_createToken()
SEC_checkToken()
```

The examples use the `gltoken` form field.

All state-changing forms should be protected with Geeklog's CSRF protection functions.

### Updated Language Templates

Generated language files include a reminder that they must be saved as:

```text
UTF-8 without a BOM
```

This allows full Unicode support, including emojis, when the database uses `utf8mb4`.

The templates also use safe fallback patterns such as the PHP null coalescing operator.

### English Code Comments

Inline comments in generated files have been standardized in English to make the templates easier to maintain and share with international developers.

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
2. Test plugin upgrades from every supported previous version.
3. Test uninstallation and data-removal behavior.
4. Test with PHP 8.1 and PHP 8.3.
5. Enable Geeklog debugging and review PHP logs.
6. Verify all permission checks.
7. Verify CSRF protection on every state-changing action.
8. Test with different Geeklog themes.
9. Validate language files as UTF-8 without a BOM.
10. Review SQL compatibility with the target database configuration.

## Additional Resources

* [Geeklog Plugin Developer Handbook](https://wiki.geeklog.net/index.php/Plugin_Developers_Handbook)
* [Geeklog Plugin API](https://wiki.geeklog.net/index.php/Plugin_API)
* [Geeklog GitHub Organization](https://github.com/Geeklog-Core)

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
