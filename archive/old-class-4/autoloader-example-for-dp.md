
Set up Composer autoloading
Create ERD for social media site



I’ll drop in a minimal SPL autoloader that maps ClassName => /classes/ClassName.php, plus a tiny usage example.

- Register the autoloader (non-namespaced classes only)
- Show example class file and usage

```php
<?php
// Place this at the entry point (e.g., index.php in your project root)

spl_autoload_register(function (string $class): void {
    // Only allow simple class names: letters, numbers, underscores
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $class)) {
        return; // ignore invalid or namespaced class names
    }

    $baseDir = __DIR__ . '/classes/';
    $file = $baseDir . $class . '.php';

    if (is_file($file)) {
        require $file;
    }
});

// Example usage:
// Assuming classes/User.php defines `class User {}`
$user = new User();
```


Notes:

- Put your class files in /classes/ with filenames matching the class name, e.g., classes/User.php -> class User {}.
- Keep class/file names case-consistent (especially important on Linux/macOS).
- 