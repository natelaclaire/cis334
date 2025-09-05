---

# Magic Methods

PHP reserves method names beginning with a double underscore (`__`) for **magic methods**. These special methods let you customize how objects behave in specific situations.  We talked about two examples last week: the constructor (`__construct`) and destructor (`__destruct`) methods. In this video, we'll discuss a few others.

## `__clone`

## `__serialize` and `__unserialize`

## `__debugInfo`

## Property and method overloading methods (`__get`, `__set`, `__isset`, `__unset`, `__call`, `__callStatic`)

## `__toString`

## `__invoke` makes an object callable like a function.

## Closing Thoughts

Except for the constructor, destructor and clone methods, magic methods must be declared **public**. Use these methods sparingly and consistently to implement advanced behaviors such as proxies or dynamic properties.

---

### Slide 13 – What’s New in PHP 8.3

**Dynamic constant fetch** lets you access class constants dynamically using variable names (e.g., `C::${'A'}`).  
**deep cloning of readonly properties** ensures that when you clone an object with a readonly property containing another object, the inner object is also cloned rather than shared.


### constructor property promotion

### Nullsafe methods and properties 

### named arguments in methods

### namespaces

### autoloaders

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