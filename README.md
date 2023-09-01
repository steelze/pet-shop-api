# Pet Shop API 🐶

### Introduction
A collection REST API endpoints for FE team to build the UI of the Pet Shop App

### Table of Contents 📖
1. <a href="#technology-stack">Technology Stack</a>
2. <a href="#application-features">Application Features</a>
3. <a href="#api-endpoints">API Endpoints</a>
4. <a href="#setup">Setup</a>
5. <a href="#testing">Testing</a>
6. <a href="#author">Packages</a>
7. <a href="#author">Author</a>
8. <a href="#license">License</a>


### Technology Stack & Tools 🧰
  - [PHP](https://www.php.net/)
  - [Laravel](https://laravel.com/)
  - MySQL
  - [Git](https://git-scm.com/) 
  - [Composer](https://getcomposer.org/) 

### Application Features 📑
* User can create account and login
* User can reset password
* User can edit their profile
* User can view and filter brands and categories
* User can view and filter products
* Admin can login
* Admin can edit and delete user profile
* Admin can create, update and delete categories
* Admin can create, update and delete brands
* Admin can create, update and delete products

### API Endpoints 📬
Method | Route | Description
--- | --- | ---
`GET` | `/api/products` | Fetch all products

### Setup 👨🏾‍💻
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

  #### Dependencies
  - [Git](https://git-scm.com/) 
  - [Composer](https://getcomposer.org/)  
  - [Laravel](https://laravel.com/)
  #### Getting Started
  - Install and setup laravel
  - Open terminal and run the following commands
    ```
    $ git clone https://github.com/steelze/pet-shop-api.git
    $ cd pet-shop-api
    $ composer install
    $ cp .env.example .env
    $ php artisan key:generate
    ```
  - Run Migration
    ```
    // Update .env file with correct DB credentials
    $ php artisan migrate --seed
    ```
  - Start Application
    ```
    $ php artisan serve
    ```
  - Visit http://localhost:8000 on your browser or Postman or http://localhost:8000/api/documentation to view the API documentation

### Testing 🧪
  ```
  $ php artisan test
  ```
  If correctly setup, all tests should pass

  ![Alt text](/public/tests.png "Test cases")
  
### Packages ✍🏾
- Exchange Rate - https://github.com/steelze/pet-shop-api.git
  
### Author ✍🏾
Odunayo Ileri Ogungbure

### License 
MIT
