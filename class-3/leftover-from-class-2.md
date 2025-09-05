---

### Slide 12 – Magic Methods

“PHP reserves method names beginning with a double underscore (`__`) for **magic methods**.  These special methods let you customize how objects behave in specific situations.  The constructor (`__construct`) and the cloning method (`__clone`) run when an object is created and cloned.  Magic methods like `__serialize` and `__unserialize` handle object serialization, while `__debugInfo` customizes debug output.  Property and method overloading methods (`__get`, `__set`, `__isset`, `__unset`, `__call`, `__callStatic`) allow you to intercept access to undefined or inaccessible members.  `__toString` lets an object be converted to a string, and `__invoke` makes an object callable like a function.  Except for the constructor, destructor and clone methods, magic methods must be declared **public**.  Use these methods sparingly and consistently to implement advanced behaviours such as proxies or dynamic properties.”

---

### Slide 13 – What’s New in PHP 8.3

**Dynamic constant fetch** lets you access class constants dynamically using variable names (e.g., `C::${'A'}`).  
**deep cloning of readonly properties** ensures that when you clone an object with a readonly property containing another object, the inner object is also cloned rather than shared.


### constructor property promotion

### Nullsafe methods and properties 

### named arguments in methods

### namespaces

### autoloaders

