# README.md

composer install

docker compose down

docker compose up --build -d

docker-compose exec php-fpm bash

docker compose exec mysql bash

##  mysql

mysql -u user_camagru -p

SHOW DATABASES;
USE camagru;
SHOW TABLES;

# Connect to MySQL container
docker compose exec mysql bash

# Login to MySQL with credentials from .env
mysql -u user_camagru -p
# Enter password when prompted: super_secret

# Show all databases
SHOW DATABASES;

# Select camagru database
USE camagru;

# Show all tables
SHOW TABLES;

# View users table structure
DESCRIBE users;

# View all users
SELECT * FROM users;

# View specific columns
SELECT username, email, confirmed FROM users;

# View reset tokens
SELECT username, email, reset_token, reset_expires FROM users;

# Exit MySQL
exit

# Exit container
exit

# Camagru

Camagru is a web application that allows users to register, manage their accounts, capture photos, overlay stickers, and share their creations in a public gallery. 

## Features

- **User Registration and Management**: Secure account creation with email confirmation and password recovery.
- **Image Capture and Editing**: Users can capture images using their webcam or upload images, overlay stickers, and save their creations.
- **Public Gallery**: A gallery displaying all user creations with pagination.
- **User Interaction**: Registered users can like and comment on images, with notifications sent to authors for new comments.
- **Responsive Design**: The application is designed to work on both desktop and mobile devices.

## Project Structure

```
camagru
├── docker
│   ├── nginx
│   │   └── nginx.conf
│   ├── php
│   │   └── Dockerfile
│   └── docker-compose.yml
├── src
│   ├── config
│   │   ├── database.php
│   │   └── email.php
│   ├── controllers
│   │   ├── AuthController.php
│   │   ├── ImageController.php
│   │   ├── GalleryController.php
│   │   └── CommentController.php
│   ├── models
│   │   ├── User.php
│   │   ├── Image.php
│   │   ├── Comment.php
│   │   └── Like.php
│   ├── public
│   │   ├── index.php
│   │   ├── css
│   │   ├── js
│   │   └── stickers
│   └── views
│       ├── auth
│       ├── editor
│       ├── gallery
│       └── templates
├── .env.example
├── .gitignore
├── composer.json
└── README.md
```

## Installation

1. Clone the repository.
2. Navigate to the project directory.
3. Copy `.env.example` to `.env` and configure your environment variables.
4. Run `docker-compose up` to start the application.

## Usage

- Access the application through your web browser.
- Register for an account to start capturing and sharing images.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for any suggestions or improvements.

## License

This project is licensed under the MIT License.