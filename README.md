# Coffee Consumption Registration API
The objective of this API is to provide functionality for the registration and reporting of coffee consumption by users.

### PROJECT PRESENTATION VIDEO: https://youtu.be/eNhzh9fWQpM

### API Documentation: https://documenter.getpostman.com/view/21559984/2s9YyqjNJS
**Technologies Used** 

- **Programming Language:** PHP (8.2.5)
- **Database:** MYSQL
- **API client:** POSTMAN

## Main Features


### POST

**User Registration:**
- **Endpoint:** POST /users
- **Description:** creation of new users.
- **Input:** JSON containing user information (name, email, password).
- **Output:**  Success or error message if the user already exists.

**User Authentication:**
- **Endpoint:** POST /login
- **Description:** Performs user authentication.
- **Input:** JSON containing the user's email and password
- **Output:** Success or error message if the user does not exist or the password is invalid.

**Coffee Consumption Registration:**
- **Endpoint:** POST /users/{id}/drink
- **Description:** Records coffee consumption for a specific user.
- **Input:** JSON containing the quantity of coffee consumed.
- **Output:** Success or error message.

### GET

**List Users:**
- **Endpoint:** users/ 
- **Description:** Lists all users registered in the system.
- **Output:** JSON containing all user information. 

**List User by ID:**
- **Endpoint:** users/{id} 
- **Description:** Lists a user registered in the system by ID.
- **Output:** JSON containing all user information. 

**User Consumption Report:**
- **Endpoint:** GET /record-history/{id}
- **Description:** Returns a report of the coffee consumption history for a specific user.
- **Output:** JSON containing dates and the quantity of times the user consumed coffee.

**Daily Consumption Ranking:**
- **Endpoint:** GET /ranking-day
- **Description:** Returns a report of coffee consumption for all users for a specific day.
- **Input:** Date of the specific day passed via the request body (e.g., "28/01/2024").
- **Sáida:** JSON containing all information from the report.

**Users Ranking for a Period:**
- **Endpoint:** GET /ranking-range
- **Description:** Returns a report of coffee consumption for all users for the specified period.
- **Input:** Start and end dates passed via the request body.
- **Sáida:** JSON containing all information from the report.

**Users Ranking for 'x' Days Ago:**
- **Endpoint:** GET /ranking-lastdays
- **Description:** Rlists the ranking of the user who drank more coffee on the last X days.
- **Input:** Start and end dates passed via the request body.
- **Sáida:** JSON containing all information from the report.

### PUT

**User Edition::**
- **Endpoint:** PUT /users/{id}
- **Description:** Allows editing of information user.
- **Input:** JSON containing the information to be updated.
- **Output:** Success or error message if editing is not allowed.

### DELETE

**Delete user:**
- **Endpoint:** DELETE /users/{id}
- **Description:** Removes the user
- **Output:** Sucesso ou mensagem de erro se a remoção não for permitida.


# Project Execution Instructions
First, create the schema and tables for the project's database, which can be found at the root of the project.
```
path: .\mosyle-project\SCHEME.sql
```

Go to the Config.php file and configure the database information.
```
path: .\mosyle-project\api\Config.php
```

To start the project, navigate to the project's api folder.
```
cd .\mosyle-project\api\
```
Inside the api folder, start the PHP server.

```
php -S localhost:7777
```

