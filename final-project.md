# Final Project

Your task for the final project is to develop a simple Web application that allows users to create, view, search, and manage data of some sort. This project will utilize the core PHP concepts covered in the course, including PHP syntax, functions, control structures, text manipulation, form handling, file handling, and arrays.

Requirements:

- The user should be able to perform the following activities:
  - List records
  - View individual records
  - Create records
  - Search records
  - View a random record
- All user input must be validated to prevent errors (e.g., empty fields, invalid characters)
- Data should be stored in one or more arrays while the application is running
- Data should be written to and read from a file in a structured format (e.g., JSON or CSV) so that it persists
- Care should be taken to properly manage scope
- Optional features for extra credit:
  - Allow user to edit records
  - Allow user to delete records
  - Other more complex features, such as AJAX, pagination, or using an external API (if you have an idea for something in this category, ask me about it before you start working on it to ensure that it will count for extra credit)

---

## **Submission Requirements**

- **Code files**: Include PHP source code and any auxiliary files (e.g., data files, images, CSS).
- **Documentation**: Provide a brief explanation of your project, including:
  - How to set up and run the application.
  - How each PHP concept is used.
  - Note optional features presented for extra credit.
- **Screenshots or Video**: Demonstrate the application in action.

---

## **Grading Rubric**

| Criteria                               | Weight |
|----------------------------------------|--------|
| Functionality (meets requirements)     | 40%    |
| Code organization and use of functions | 20%    |
| Proper use of PHP features             | 20%    |
| Input validation and error handling    | 10%    |
| Creativity and user interface          | 10%    |

---

## **Learning Goals**

By completing this project, students will:

- Practice building dynamic web applications using PHP.
- Reinforce their understanding of PHP functions, control structures, and arrays.
- Gain experience with handling user input and file operations in PHP.
- Develop problem-solving skills for real-world web development scenarios.

---

## **Example Final Project:** **"Dynamic Recipe Manager"**

### **Objective:**  

Develop a web application that allows users to create, view, search, and manage recipes.

### **Project Requirements**

1. **Homepage**  
   - Display a list of available recipes stored in a file or an array.
   - Provide navigation links to add a new recipe, search for recipes, or view a random recipe.

2. **Add a Recipe**  
   - Create a form that collects the following input:
     - Recipe name
     - Number of servings
     - Ingredients (comma-separated)
     - Steps or instructions
   - Validate user input to ensure all fields are filled out.
   - Save the recipe details to a file (e.g., `recipes.txt`) in a structured format (e.g., JSON or CSV).

3. **View Recipes**  
   - Read the `recipes.txt` file to retrieve and display all stored recipes.
   - Show the recipe name, ingredients, and instructions.

4. **Search Recipes**  
   - Implement a search feature that allows users to search for recipes by name or ingredient.
   - Use string parsing and regular expressions to find matches.
   - Display matching recipes in a user-friendly format.

5. **Random Recipe**  
   - Implement functionality to select and display a random recipe from the list.

6. **Edit and Delete Recipes** (Optional)  
   - Allow users to edit or delete existing recipes.
   - Use control structures to handle updates or deletions from the file.

7. **Scale Recipe** (Optional)
   - Give users the option of viewing the recipe with a modified number of servings, in which case the ingredient amounts will be modified appropriately.

8. **User Input Validation**  
   - Validate all user inputs to prevent errors (e.g., empty fields, invalid characters).

9.  **File and Directory Handling**  
   - Store recipe data in a text file or JSON file.
   - Use PHP functions to read from and write to the file.

10. **Array Manipulation**  
   - Use arrays to store and manipulate recipes temporarily in the script.

11. **Global Variables and Scope**  
    - Use PHP's autoglobals (e.g., `$_POST`, `$_GET`) for form and hyperlink handling.
    - Ensure proper scope management in your functions.
