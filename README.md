# Manage currency rate 

Current project is working wit API provided by https://currencylayer.com and store them in MySql database.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine

### Requirements

* PHP 7.1
* MySql 5.7
* Account at [Currency Layer](https://currencylayer.com)

### Installing

A step by step series of examples that tell you how to get a development env running

Configure your project environment:

    Copy ".env.dist" as ".env" in project root directory
    Set correct data to CURRENCY_LAYER_ACCESS_KEY and DATABASE_URL in ".env" file
    Configure other properties in ".env", depends on you preferences

You can create database manually or run a command:

    ./bin/console doctrine:database:create

To get a proper DB structure run migration script:

    ./bin/console doctrine:migrations:migrate

### Testing

Application use phpunit test framework that could be simply runned by command:

    ./bin/phpunit

### Usage

To fetch data from API and save it in databases run command:

    ./bin/console currency:get-rates

If you have question of possible arguments then run next command to get manuel:

    ./bin/console currency:get-rates
