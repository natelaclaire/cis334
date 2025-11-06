---
layout: default
title: 11.4 Class 11 Reinforcement Exercises
nav_order: 4
---

# 11.4 Class 11 Reinforcement Exercises

## Exercise 11-1: Building a Login System with PHP Sessions

In this exercise, we’ll build a simple **login system** that uses PHP **sessions** to remember who’s logged in — and display a personalized welcome page. This demonstration will help you understand how session management powers secure, stateful web applications.

### **Project Overview**

Here’s what we’ll build:

1. A **login form** that collects a username and password.
2. A **PHP script** that checks those credentials.
3. A **session** to remember the user after login.
4. A **personalized welcome page**.
5. A **logout page** that ends the session.

We’ll keep the credentials simple — no database yet — just an associative array in PHP.

### **Step 1: Creating the Login Form**

Let’s start with a file named `index.php`. Create a new folder inside the `exercises` folder called `11-1-login-system`, and inside that, create `index.php` with the following code:

```php
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <h2>Login</h2>
  <form method="post" action="authenticate.php">
    <p>
      Username: <input type="text" name="username" required>
    </p>
    <p>
      Password: <input type="password" name="password" required>
    </p>
    <p>
      <input type="submit" value="Login">
    </p>
  </form>
</body>
</html>
```

This form sends the username and password to a script named `authenticate.php`, which we’ll create next.

### **Step 2: Authenticating the User**

Create a new file named `authenticate.php` in the same folder and add this code to it:

```php
<?php
session_start();

// Sample user data (this would normally come from a database)
$users = [
  "alex" => "pass123",
  "jordan" => "code456"
];

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

if (isset($users[$username]) && $users[$username] === $password) {
  $_SESSION["username"] = $username;
  header("Location: welcome.php");
  exit();
} else {
  echo "<p>Invalid username or password. Try again.</p>";
  echo "<p><a href='index.php'>Back to Login</a></p>";
}
?>
```

Here, we check whether the submitted username exists in our array and whether the password matches. If it does, we store the username in `$_SESSION` — that’s what identifies the user across pages — and then redirect them to `welcome.php`.

### **Step 3: Creating the Personalized Page**

Now, let’s create a personalized page that greets the logged-in user. Create a new file named `welcome.php` and add the following code:

```php
<?php
session_start();

if (!isset($_SESSION["username"])) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
</head>
<body>
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
  <p>You are now logged in.</p>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>
```

Here, we first check if `$_SESSION["username"]` is set. If not, we redirect the visitor back to the login page — this prevents unauthorized access. Otherwise, we safely display the username and a logout link.

### **Step 4: Logging Out**

Finally, we’ll let users log out by ending the session. Create a new file named `logout.php` and add this code:

```php
<?php
session_start();
session_unset();     // remove all session variables
session_destroy();   // end the session
header("Location: index.php");
exit();
?>
```

When the user clicks “Logout,” this script clears all session data and redirects them back to the login page.

### **Step 5: Testing the System**

Let’s test it out.

1. Start Apache if it isn't already and then use the Ports tab to connect to the port and view our simple file explorer. Open `/exercises/11-1-login-system/index.php` in your browser.
2. Enter a valid username and password — “alex” and “pass123.”
3. You’ll be redirected to `welcome.php`, where your username appears.
4. Click “Logout,” and the cookie remains but the session data is gone — try reloading `welcome.php`, and you’ll be redirected to the login page again.

## Exercise 11-2: Playing with Cookies

In this exercise, you will create a simple PHP application that uses cookies to remember a user's preferred background color for the webpage.

### **Project Overview**

Here’s what we’ll build:

1. A form where users can select their preferred background color.
2. A PHP script to save this preference in a cookie.
3. A way to read the cookie and apply the background color when the user returns.

### **Step 1: Creating the Color Selection Form**

Create a new file named `11-2-color-preference.php` in the `exercises` folder and add the following code:

```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Color Preference</title>
</head>
<body>
  <h2>Select Your Preferred Background Color</h2>
  <form method="POST" action="11-2-color-preference.php">
    <label for="color">Choose a color:</label>
    <select name="color" id="color">
      <option value="white">White</option>
      <option value="lightblue">Light Blue</option>
      <option value="lightgreen">Light Green</option>
      <option value="lightcoral">Light Coral</option>
    </select>
    <button type="submit">Save Preference</button>
    <button type="submit" name="reset" value="1">Reset to Default</button>
  </form>
</body>
</html>
```

### **Step 2: Saving the Color Preference in a Cookie**

Add the following PHP code at the top of `11-2-color-preference.php` to handle form submissions and set the cookie:

```php
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['reset'])) {
        // Reset the cookie
        setcookie("bgcolor", "", time() - 3600); // Expire the cookie
        header("Location: 11-2-color-preference.php");
        exit();
    }

    $color = $_POST["color"] ?? "white";
    setcookie("bgcolor", $color, time() + 86400); // 1 day expiration
    header("Location: 11-2-color-preference.php");
    exit();
}
?>
```

### **Step 3: Applying the Background Color from the Cookie**

Add the following code just before the closing `</head>` tag to read the cookie and apply the background color:

```php
<?php
$bgcolor = $_COOKIE["bgcolor"] ?? "white";
echo "<style>body { background-color: $bgcolor; }</style>";
?>
```

### **Step 4: Testing the Application**

1. Open `11-2-color-preference.php` in your browser.
2. Select a background color from the dropdown and submit the form.
3. Refresh the page, and you should see the selected background color applied.
4. Close the browser and reopen it to see if the color preference persists (it should, as long as the cookie hasn't expired).
5. Try changing the color again to see how the cookie updates.

## Exercise 11-3: Nag Counter

Create a document with a “nag” counter that reminds users to register. Save the counter in a cookie and display a message reminding users to register every fifth time they visit your site. Create a form in the body of the document that includes text boxes for a user’s name and e-mail address along with a Registration button. Normally, registration information would be stored in a database. For simplicity, this step will be omitted from this exercise. After a user fills in the text boxes and clicks the Registration button, delete the nag counter cookie and replace it with cookies containing the user’s name and e-mail address. After registering, display the name and e-mail address cookies whenever the user revisits the site. Save the file as `11-3-nag-counter.php` in the `exercises` folder.

## Exercise 11-4: Guessing Game

You can use PHP’s rand() function to generate a random integer. The `rand()` function accepts two arguments that specify the minimum and maximum integer to generate, respectively. For example, the statement `$randNum = rand(10, 20)` generates a random integer between 10 and 20 and assigns the number to the `$randNum` variable. Create a guessing game that uses sessions to store a random number between 0 and 100, along with the number of guesses the user has attempted. Each time the user guesses wrong, display the number of times the user has guessed. Include a Give Up link that displays the generated number for the current game. Also include a Start Over link that deletes the user session and uses the `header("location:URL")` function to navigate to the main page. Save the file as `11-4-guessing-game.php` in the `exercises` folder.

## Exercise 11-5: Products You've Viewed

Create a simple web application that displays a list of products. When a user clicks on a product, store the product ID in a cookie. On the main page, display a list of products the user has viewed based on the product IDs stored in the cookie. Limit the number of products displayed to the last five viewed products.

### **Step 1: Creating the Product List**

Create a new folder named `11-5-products` in the `exercises` folder. Inside the folder, create a file named `products.php` and add the following code:

```php
<?php
$products = [
  1 => ["name" => "Product A", "description" => "Description for Product A"],
  2 => ["name" => "Product B", "description" => "Description for Product B"],
  3 => ["name" => "Product C", "description" => "Description for Product C"],
  4 => ["name" => "Product D", "description" => "Description for Product D"],
  5 => ["name" => "Product E", "description" => "Description for Product E"]
];
```

### **Step 2: Displaying the Products and Viewed Products**

Inside the same folder, create a file named `index.php` and add the following code:

```php
<?php
require 'products.php';

$viewedProducts = isset($_COOKIE["viewed_products"]) 
                ? explode(",", $_COOKIE["viewed_products"]) 
                : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products</title>
</head>
<body>
    <h2>Product List</h2>
    <ul>
        <?php foreach ($products as $id => $product): ?>
        <li><a href="product-detail.php?id=<?php echo $id; ?>">
            <?php echo htmlspecialchars($product["name"]); ?>
        </a></li>
        <?php endforeach; ?>
    </ul>
    
    <h3>Products You've Viewed:</h3>
    <ul>
        <?php
        $viewedProducts = array_slice(array_unique($viewedProducts), -5);
        foreach ($viewedProducts as $productId):
            if (isset($products[$productId])):
            ?>
            <li><?php echo htmlspecialchars($products[$productId]["name"]); ?></li>
            <?php
            endif;
        endforeach;
        ?>
    </ul>
</body>
</html>
```

### **Step 2: Creating the Product Detail Page**
Create a new file named `product-detail.php` in the same folder and add the following code:

```php
<?php
require 'products.php';

$productId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($productId && isset($products[$productId])) {
    $viewedProducts = isset($_COOKIE["viewed_products"]) 
                    ? explode(",", $_COOKIE["viewed_products"]) 
                    : [];
    $viewedProducts[] = $productId;
    setcookie("viewed_products", implode(",", $viewedProducts), time() + (86400 * 30)); // 30 days expiration
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($products[$productId]["name"] ?? "Product Detail"); ?></title>
</head>
<body>
  <h2>Product Detail</h2>
  <?php if (isset($products[$productId])): ?>
    <h3><?php echo htmlspecialchars($products[$productId]["name"]); ?></h3>
    <p><?php echo htmlspecialchars($products[$productId]["description"]); ?></p>
  <?php else: ?>
    <p>Product not found.</p>
  <?php endif; ?>

  <p><a href="index.php">Back to Product List</a></p>
</body>
</html>
```

### **Step 3: Add Links to Products You've Viewed**

In `index.php`, we have already included a section that displays the products the user has viewed based on the product IDs stored in the cookie. This section is located under the "Products You've Viewed" heading. On your own, add links to `product-detail.php` for each viewed product to allow users to revisit the product details.

### **Step 4: Testing the Application**

1. Open `/exercises/11-5-products/index.php` in your browser.
2. Click on different products to view their details.
3. Go back to `index.php` and check the "Products You've Viewed" section to see your recently viewed products.

## Commit and Sync Changes

After completing the exercises, remember to commit your changes to your local Git repository and sync them with your remote repository on GitHub. This ensures that your work is backed up and accessible from anywhere, and that I can see and grade it. Then submit the URL of your GitHub repository through the assignment submission link above.