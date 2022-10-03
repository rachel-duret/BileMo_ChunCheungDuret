# P7_ChunCheungDuret_BileMo Formation PHP/Symfony Open Classrooms project 7

This is a project from Open Classrooms cours Developper application-PHP/Symfony
project 7

## About the Project

This project is is to create an API for the clients of BileMo company who can
fetch the catalog of cellphone in business to bussiness.

### This project needs

Here is the list of fonctions that should be accessible from your website:

-Consult the list of bileMo product -Details of a BileMo produuct -Consult the
list of registered users linked to a client on the website -Consult the details
of a registered user linked to a client -Add a new user linked to a client
-Delete a user added by a client -Only refenced clients can access the API, API
client must be authenticated

## Built With

-PHP -Composer -Symfony6 -Lexik JWT- Authentication-Bundle -NelmioBundle
API-Doc-Bundle -Doctrine/Bundle

## Requirements

-PHP >= 8.1 -Web server -Composer >= 2.3.10 -Symfony >= 6.1 -Mysql >= 5.7.24

## Installation

-Installation and Configuration for web server. Here I'm using MAMP
[MAMP](https://www.mamp.info/en/downloads/)

-Clone the repo
[ProjetRepo](https://github.com/rachel-duret/BileMo_ChunCheungDuret.git)
-Symfony install [Symfony](https://symfony.com/doc/current/setup.html) -Get into
your project directory start your web server -Install libraries -composer
install -Set up the database -Create .env.local file following .env file to
configure the appropriates values

### JWT Keys

-For generate the private key run the command: openssl genpkey -out
config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -For
generate the private key run the command: openssl pkey -in
config/jwt/private.pem -out config/jwt/public.pem -pubout

### Demo data

-To Add some demo data run command : php bin/console doctrine:fixtures:load

### Authors

-[@RachelDuret](https://github.com/rachel-duret)

### Badges

[Codacy](https://app.codacy.com/gh/rachel-duret/BileMo_ChunCheungDuret/dashboard)
