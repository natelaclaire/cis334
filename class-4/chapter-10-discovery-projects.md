---
layout: default
title: 4.1 Chapter 10 Discovery Projects
nav_order: 1
---

# 4.1 Chapter 10 Discovery Projects

## Discovery Project 10=1

As you have probably guessed, we're going to be creating some classes this week. In fact, we're going to be converting our form-building and -handling functions to a set of classes that demonstrate the OOP concept of encapsulation by bringing all of the data and functionality for each field into an object - each field in our form will be an object, and the form itself will be an object that stores the fields in a property.

Since we're already using Composer for our discovery projects, we'll update the `composer.json` file so that it knows where to find our classes and can autoload them as needed.

1. Before you begin, create a new branch in your repository called `chapter-10`. To do that, press Ctrl-Shift-P to open the command palette, type "branch", and choose "Git: Create Branch..." when it appears. Then type `chapter-10` as the branch name when prompted and press Enter.
2. In the root of your repository, create a new folder called `app`.
3. Open the `composer.json` file that is in the root of your repo. It should currently contain a `require` section. You'll need to add a new section called `autoload` and the two sections need to be separated by a comma. The new section should appear exactly as shown in the sample below, which instructs Composer to autoload any classes in the `App` namespace from the `app/` folder, using PSR-4 to understand the folder structure. Don't worry about slight differences in the `require` section, such as different version numbers.

```json
{
    "require": {
        "phpmailer/phpmailer": "^6.9",
        "erusev/parsedown": "^1.7",
        "symfony/yaml": "^7.2"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
}
```

3. Next, open the Terminal tab at the bottom of the window (Ctrl-\` will open it) and enter the following to regenerate the autoload files based on your changes to `composer.json`:

```bash
composer dump-autoload
```

4. Remember to stage and commit your changes, adding "Discovery Project 10-1" as the commit message, then sync the changes.

## Discovery Project 10=2

Next we'll replace our `htmlAttribute()` with a class called `HtmlAttribute`. This class will be in the `App\Forms` namespace.

1. Inside the `app` folder create a new folder named `Forms`.
2. Inside the `Forms` folder, create a file named `HtmlAttribute.php`.
3. Inside the `HtmlAttribute.php` file, add the following:

```php
<?php
namespace App\Forms; // define the namespace for this class

class HtmlAttribute
{
    // uses Constructor Property Promotion to define and initialize the properties
    public function __construct(
        public string $name,
        public ?string $value = null
        )
    {
    }

    // outputs the attribute as a string, as just the name for binary attributes or a name="value" pair for others
    public function __toString(): string
    {
        return $this->value === null ? $this->name : sprintf('%s="%s"', $this->name, htmlspecialchars($this->value));
    }
}
```

4. Open the `includes/functions.php` file and remove the `htmlAttribute()` function.
5. Look through the `includes/functions.php` file and replace all calls to the `htmlAttribute()` function with object instantiations of the `HtmlAttribute` class. They should stand out because VS Code should mark them as errors thanks to the PHP Intelephense extension. For example:

```php
$htmlElement[] = htmlAttribute('for', $for);
// becomes
$htmlElement[] = new HtmlAttribute('for', $for);
```

6. Test your form and ensure that it still works.
7. Remember to stage and commit your changes, adding "Discovery Project 10-2" as the commit message, then sync the changes.

## Discovery Project 10=3

We have a handful of functions that build different types of form fields. Each of them contains much of the same code, with minor differences for the different field types. This is a great example of where inheritance shines! In this project, we'll create an abstract superclass to provide the shared functionality and require that the subclasses provide specific methods.

1. Inside the `app/Forms` folder, create a file named `FormField.php`.
2. Paste the following into the file:

```php
<?php
namespace App\Forms;

abstract class FormField {
    // these properties won't get values via the constructor's parameters
    protected ?string $value = null;
    protected ?string $validationMessage = null;

    // uses Constructor Property Promotion for most properties
    public function __construct(
        protected string $label,
        protected string $name,
        array $validationResult = [], // this isn't promoted to a property, just used in the constructor
        protected ?int $minLength = null,
        protected ?int $maxLength = null,
        protected ?string $placeholder = null,
        protected bool $required = false,
        protected bool $readOnly = false,
        protected array $classes = ['form-control'],
        protected ?string $id = null
    ) {
        // get the posted value when instantiated
        $this->value = $this->getPostedValue(); 

        // if no ID was provided, use the name as the ID
        if (empty($id)) {
            $this->id = $this->name;
        }

        // if there's a validation message for this field, set it
        if (isset($validationResult[$name])) {
            $this->setValidationMessage($validationResult[$name]);            
        }
    }

    public function setValidationMessage(?string $message): void {
        $this->validationMessage = $message;
    }

    // accessors
    public function getValue(): ?string {
        return $this->value;
    }
    public function setValue(?string $value): void {
        $this->value = $value;
    }
    public function getValidationMessage(): ?string {
        return $this->validationMessage;
    }
    public function getId(): ?string {
        return $this->id;
    }
    public function getName(): string {
        return $this->name;
    }
    public function isRequired(): bool {
        return $this->required;
    }
    public function isReadOnly(): bool {
        return $this->readOnly;
    }
    public function getLabel(): string {
        return $this->label;
    }
    // Returns the array of CSS classes, adding 'is-invalid' if there's a validation message
    public function getClasses(): array {
        if ($this->validationMessage) {
            if (!in_array('is-invalid', $this->classes)) {
                $this->classes[] = 'is-invalid';
            }
        }
        return $this->classes;
    }
    public function getPlaceholder(): ?string {
        return $this->placeholder;
    }
    public function getMinLength(): ?int {
        return $this->minLength;
    }
    public function getMaxLength(): ?int {
        return $this->maxLength;
    }

    // each subclass must implement these methods
    // the classes will store the entered value, perform validation, and render the HTML for the field
    abstract protected function getPostedValue(): ?string;
    abstract public function validate(): ?string;
    abstract public function render(): string;

    // utility methods for rendering HTML
    // Render the label as HTML
    public function renderLabel(array $classes = array('form-label')): string {
        $htmlElement = ['label'];

        $htmlElement[] = new HtmlAttribute('for', $this->id);

        if (count($classes)) {  
            // classes in the class attribute are space-separated, so  
            // we’ll take an array and implode it with spaces as the "glue"
            $htmlElement[] = new HtmlAttribute('class', implode(' ', $classes));
        }

        $htmlElementString = '<'.implode(' ', $htmlElement).'>';

        $htmlBlock = $htmlElementString.$this->label.'</label>';

        return $htmlBlock;
    }

    // Render the validation message, if any, as HTML
    public function renderValidationMessage(): string {
        return $this->validationMessage ? '<div id="validationFeedback-'.$this->name.'" class="invalid-feedback">'.
                htmlspecialchars($this->validationMessage).'</div>' : '';
    }

    // Render the HTML attributes for the form field
    // $extra is an array of HtmlAttribute objects to add to the standard attributes
    public function renderAttributes(array $extra = []): string {
        $attrs = [
            new HtmlAttribute('name', $this->name),
            new HtmlAttribute('id', $this->id),
        ];
        if ($this->required) $attrs[] = new HtmlAttribute('required');
        if ($this->readOnly) $attrs[] = new HtmlAttribute('readonly');
        if (!empty($this->placeholder)) $attrs[] = new HtmlAttribute('placeholder', $this->placeholder);
        if (isset($this->minLength)) $attrs[] = new HtmlAttribute('minlength', $this->minLength);
        if (isset($this->maxLength)) $attrs[] = new HtmlAttribute('maxlength', $this->maxLength);
        $classes = $this->getClasses();
        if (count($classes)) $attrs[] = new HtmlAttribute('class', implode(' ', $classes));
        $attrs = array_merge($attrs, $extra);
        return implode(' ', $attrs);
    }

    // Render the value for display in an email or on a webpage
    public function renderValue(): string
    {
        return $this->value ?? '';
    }
}
```

3. We don't have anything to test yet. Remember to stage and commit your changes, adding "Discovery Project 10-3" as the commit message, then sync the changes.

## Discovery Project 10=4

Now that we have our `FormField` abstract class with the shared functionality and properties, it's time to extend it with concrete classes for each field type.

1. Create a new file named `TextField.php` in the `app/Forms` folder and paste the following into it. Note that it doesn't provide a constructor because the superclass constructor covers all of the requirements.

```php
<?php
namespace App\Forms;

class TextField extends FormField {
    protected function getPostedValue(): ?string {
        return filter_input(INPUT_POST, $this->name, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }
    public function validate(): ?string {
        $val = trim((string)$this->value);
        if ($this->required && $val === '') {
            $this->validationMessage = 'This field is required.';
        }
        $len = strlen($val);
        if (isset($this->minLength) && $len < $this->minLength) {
            $this->validationMessage = "Must be at least {$this->minLength} characters.";
        }
        if (isset($this->maxLength) && $len > $this->maxLength) {
            $this->validationMessage = "Must be at most {$this->maxLength} characters.";
        }
        return $this->validationMessage;
    }
    public function render(): string {
        $attrs = $this->renderAttributes([new HtmlAttribute('type', 'text')]);
        $valueAttr = $this->value !== null && $this->value !== '' ? new HtmlAttribute('value', $this->value) : '';
        $input = '<input ' . $attrs . ' ' . $valueAttr . '>';
        $html = '<div class="mb-3">' . $this->renderLabel() . $input . $this->renderValidationMessage() . '</div>';
        return $html;
    }
}
```

2. Create a new file named `TextareaField.php` in the `app/Forms` folder and paste the following into it:

```php
<?php
namespace App\Forms;

class TextareaField extends FormField {
    public function __construct(
        string $label, // the first ten are passed to the parent constructor
        string $name,
        array $validationResult = [],
        ?int $minLength = null,
        ?int $maxLength = null,
        ?string $placeholder = null,
        bool $required = false,
        bool $readOnly = false,
        array $classes = ['form-control'],
        ?string $id = null,
        protected string $wrap = 'soft', // the last three are promoted to subclass properties
        protected int $cols = 20,
        protected int $rows = 2
    ) {
        // call the parent constructor
        parent::__construct($label, $name, $validationResult, $minLength, $maxLength, $placeholder, $required, $readOnly, $classes, $id);
    }
    protected function getPostedValue(): ?string {
        return filter_input(INPUT_POST, $this->name, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }
    public function validate(): ?string {
        $val = trim((string)$this->value);
        if ($this->required && $val === '') {
            $this->validationMessage = 'This field is required.';
        }
        $len = strlen($val);
        if (isset($this->minLength) && $len < $this->minLength) {
            $this->validationMessage = "Must be at least {$this->minLength} characters.";
        }
        if (isset($this->maxLength) && $len > $this->maxLength) {
            $this->validationMessage = "Must be at most {$this->maxLength} characters.";
        }
        return $this->validationMessage;
    }
    public function render(): string {
        $attrs = $this->renderAttributes([
            new HtmlAttribute('wrap', $this->wrap),
            new HtmlAttribute('cols', (string)$this->cols),
            new HtmlAttribute('rows', (string)$this->rows)
        ]);
        $input = '<textarea ' . $attrs . '>' . htmlspecialchars((string)$this->value) . '</textarea>';
        $html = '<div class="mb-3">' . $this->renderLabel() . $input . $this->renderValidationMessage() . '</div>';
        return $html;
    }
    #[\Override]
    public function renderValue(): string
    {
        // convert newlines to <br> for HTML display
        return nl2br((string)$this->value);
    }
}
```

3. Create a new file named `EmailField.php` in the `app/Forms` folder and paste the following into it:

```php
<?php
namespace App\Forms;

class EmailField extends FormField {
    protected function getPostedValue(): ?string {
        return filter_input(INPUT_POST, $this->name, FILTER_SANITIZE_EMAIL);
    }
    public function validate(): ?string {
        $val = trim((string)$this->value);
        if ($this->required && $val === '') {
            $this->validationMessage = 'This field is required.';
        }
        $len = strlen($val);
        if (isset($this->minLength) && $len < $this->minLength) {
            $this->validationMessage = "Must be at least {$this->minLength} characters.";
        }
        if (isset($this->maxLength) && $len > $this->maxLength) {
            $this->validationMessage = "Must be at most {$this->maxLength} characters.";
        }
        if ($val && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->validationMessage = 'Invalid email address.';
        }
        return $this->validationMessage;
    }
    public function render(): string {
        $attrs = $this->renderAttributes([new HtmlAttribute('type', 'email')]);
        $valueAttr = $this->value !== null && $this->value !== '' ? new HtmlAttribute('value', $this->value) : '';
        $input = '<input ' . $attrs . ' ' . $valueAttr . '>';
        $html = '<div class="mb-3">' . $this->renderLabel() . $input . $this->renderValidationMessage() . '</div>';
        return $html;
    }
}
```

4. Using the above classes as a guide, if you have another function for creating an additional type of field of your choosing (you should), create a class that extends `FormField` and replaces that function. For example, if you have a `numberField()` function, you'll create a `NumberField` class.
5. We're not quite ready to test yet. Remember to stage and commit your changes, adding "Discovery Project 10-4" as the commit message, then sync the changes.

## Discovery Project 10=5

Now that we have our form field classes set up, it's time to put it all together with a class for managing our contact form.

1. Create a new file named `ContactForm.php` in the `app/Forms` folder and paste the following into it. Note that it doesn't extend `FormField`.

```php
<?php
namespace App\Forms;

class ContactForm {
    public array $validationResult = [];

    // fields is an associative array of FormField objects, keyed by field name,
    // so we can easily access specific fields like $this->fields['email']
    // and adding new fields is just a matter of adding to the array that is passed in
    public function __construct(public array $fields = []) {
        
    }

    public function validate(): bool {
        $this->validationResult = [];
        foreach ($this->fields as $name => $field) {
            // validate each field and collect any error messages
            $error = $field->validate();
            if ($error) {
                $this->validationResult[$name] = $error;
            }
        }

        // return true if no validation errors
        return empty($this->validationResult);
    }

    public function render(): void {
        echo '<form method="post" action="'.constructUrl('contact-us').'" class="row g-3">';
        if (count($this->validationResult)) {
            if (isset($this->validationResult['sendingError'])) {
                echo '<div class="alert alert-danger" role="alert">'.$this->validationResult['sendingError'].'</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Something is not right. Please check the message'.(count($this->validationResult)==1?'':'s').' below, make any necessary corrections, and try again. Thank you!</div>';
            }
        }

        // render each field
        foreach ($this->fields as $field) {
            echo $field->render();
        }
        echo '<button type="submit" class="btn btn-primary">Send</button>';
        echo '</form>';
    }

    public function writeEmailLog(): void {
        $logFile = APP_PATH.'logs/email-'.date('Y-m-d').'.log';
        $logData = [
            'Timestamp' => date('Y-m-d H:i:s'),
        ];

        // add each field's label, value, and validation error to the log data array
        foreach ($this->fields as $field) {
            $logData[$field->getLabel()] = $field->getValue();
            if ($field->getValidationMessage()) {
                $logData['Validation Error - ' . $field->getLabel()] = $field->getValidationMessage();
            }
        }
        $logEntry = '';
        foreach($logData as $key => $value){
            $logEntry .= $key . ': ' . $value . PHP_EOL;
        }
        $logEntry .= "----------------------------------------------" . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    public function sendEmail(): bool|string {
        $toAddress = 'your.email@maine.edu';
        $fromAddress ='webform@myserver.mydomain.com';
        $fromName = 'Your Web Site';
        $subject = 'Message from Web site';

        $replyTo = $this->fields['email']->getValue();
        $first = $this->fields['first']->getValue() ?? '';
        $html = '';

        // build the email body
        foreach ($this->fields as $field) {
            $html .= '<p><strong>'.$field->getLabel().':</strong> '.$field->renderValue().'</p>';
        }

        // need to use the fully qualified name here because we're not using "use"
        // and must start with a backslash to indicate the global namespace since
        // we're inside a namespace
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->SMTPAuth = false;
            $mail->Port = 25;
            $mail->setFrom($fromAddress, $fromName);
            $mail->addReplyTo($replyTo, $first);
            $mail->addAddress($toAddress);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            return $mail->ErrorInfo;
        }
    }
}
```

2. Open `includes/contact-us.php` and replace its contents with the following, but be sure to add your custom field as well:

```php
<div class="container px-4 py-5">
    <h1>Contact Us</h1>
    <?php
    use App\Forms\{ContactForm, TextField, EmailField, TextareaField};

    // create the form with fields
    $form = new ContactForm([
        'first' => new TextField('First Name', 'first', [], 1, 100, null, true),
        'email' => new EmailField('Email Address', 'email', [], 5, 255, null, true),
        'message' => new TextareaField('Message', 'message', [], 5, 600, null, true, false, ['form-control'], null, 'soft', 20, 3),
        // add your field to this array in the proper order
    ]);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($form->validate()) {
            $form->writeEmailLog();
            $sendStatus = $form->sendEmail();
            if ($sendStatus === true) {
                echo '<div class="alert alert-success" role="alert">Thank you for reaching out! We&rsquo;ll be in touch within 48 hours.</div>';
            } else {
                $form->validationResult['sendingError'] = "<p>There was a problem sending the message: {$sendStatus}</p><p>Please double-check what you entered and try again in a few seconds. We apologize for the inconvenience.</p>";
                $form->render();
            }
        } else {
            // log the submission even if validation failed so we can see what went wrong
            $form->writeEmailLog();
            $form->render();
        }
    } else {
        $form->render();
    }
    ?>
</div>
```

3. We're ready to test! Try submitting the form again and see what happens. Once it is working, remember to stage and commit your changes, adding "Discovery Project 10-5" as the commit message, then sync the changes.
