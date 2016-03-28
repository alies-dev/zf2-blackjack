# Introduction
This is a simple application using the ZF2 for backend and Angular 1.4 for frontend.

# Game rules
This game should allow the user to play against a computer dealer that plays automatically.
As each game finishes the system keep track of wins/losses/draws and scores.
The player is able to view historical scores at any time.
For reasons of simplicity, the player have 2 options only: hit or stand. Insurance, splitting, etc. are not implemented. The dealer should stand on 17.

# Installation

Backend dependencies:

`composer install`

Frontend dependencies:

`bower install`

DB:

`php public/index.php orm:schema-tool:update --force`

Run PHP web-server (optionally, >=PHP 5.4):

`php -S 0.0.0.0:8080 -t public/ public/index.php`