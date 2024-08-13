# blog-post
created blog folder and inside created post.php file

# create database
CREATE DATABASE blogs;

# create table
CREATE TABLE posts
(
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	content TEXT NOT NULL,
	author VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

# api collection

# list of posts
http://localhost/blog/post.php using GET method

# create of post
http://localhost/blog/post.php using POST method

# view of post
http://localhost/blog/post.php/1 using GET method

# update of post
http://localhost/blog/post.php/1 using PUT method

# delete of post
http://localhost/blog/post.php/1 using DELETE method