<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Ayo Indonesia Technical Test

This project is the technical test for join Ayo Indonesia, and hope you enjoy with this project.

## Requirement

- [Composer](https://getcomposer.org/).
- Code editor for doing coding activities [Visual Studio Code](https://code.visualstudio.com/) or [Sublime](https://www.sublimetext.com/) or [Atom](https://atom.io/).
- Php and Web server for running laravel in web browser, can use [XAMPP](https://www.apachefriends.org/) or [Laragon](https://laragon.org/).
- [MySQL](https://www.mysql.com/downloads/) for Database Management System.

<p align="right">(<a href="#top">back to top</a>)</p>

## Environment

To set up environment, you'll need .env file on your computer project. use `cp .env.example .env` from the command prompt to create environment files.

```bash
# Driver Database
DB_CONNECTION=mysql 

# Host
DB_HOST=127.0.0.1

# Port
DB_PORT=3306 

# Name Database
DB_DATABASE=database

# Username
DB_USERNAME=root 

# Password
DB_PASSWORD=password
```

<p align="right">(<a href="#top">back to top</a>)</p>

## How To Use

To clone and run this application, you'll need [Git](https://git-scm.com) installed on your computer. From your command line:

```bash
# Clone this repository
$ git clone https://github.com/el-fath/ayoindonesia.git

# Go into the repository
$ cd ayoindonesia

# Create the database
$ create mysql database with name `ayoindonesia`

# Install dependencies
$ composer install

# Run migration
$ php artisan migrate

# Install passport for authorization
$ php artisan passport:install

# Run seeder
$ php artisan db:seed

# Run the app
$ php artisan serve
```

> **Note**
> For the documentation you can access `http://127.0.0.1:8000/api/documentation` after runnning the app,
> or you can import json collection file in `storage/api-docs/api-docs.json` to your Postman / Insomnia App

<p align="right">(<a href="#top">back to top</a>)</p>
