![Vonage][logo]

# Verify User Registrations with Symfony

Users registering with false information can be a pest, which is especially the case when registering with phone numbers that are expected to be contactable. Vonage's Verify API provides a solution to this by enabling you to confirm that the phone number is correct and owned by the user. The API takes a phone number, sends a pin code to that phone number and expects it to be relayed back through the correct source.

This branch is the starter branch for the accompanying post at: [Blog post url here](#)

**Table of Contents**

- [Prerequisites](#prerequisites)
- [Getting Started](#getting-started)
  - [Cloning the Repository and Checking out the Starter Branch](#cloning-the-repository-and-check-out-the-starter-branch)
  - [Installing Third Party Libraries](#installing-third-party-libraries)
  - [Database Credentials](#database-credentials)
  - [Running Docker](#running-docker)
  - [Running Database Migrations](#Running-database-migrations)
  - [Test Run the Application](#test-run-the-application)
- [Code of Conduct](#code-of-conduct)
- [Contributing](#contributing)
- [License](#license)

## Prerequisites

- [Docker](https://www.docker.com/)
- [Node Package Manager (NPM)](https://www.npmjs.com/get-npm)
- [A Vonage (formally Nexmo) account](https://dashboard.nexmo.com/sign-up?utm_source=DEV_REL&utm_medium=github&utm_campaign=symfony-5-verify-api)
- [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

## Getting Started

### Cloning the Repository and Checking out the Starter Branch

Run the following three commands to clone this repository, change directory into the repository directory, and then checkout to the starter branch.

```
git clone git@github.com:nexmo-community/verify-user-registrations-with-symfony.git
cd verify-user-registrations-with-symfony
git checkout starter
```

### Installing Third Party Libraries

Several third party libraries already defined and need to be installed, both via Composer and yarn packages.

Change directory into `symfony/` and run the following three commands:

```
composer install
yarn install
yarn run dev
```

### Database Credentials

Within the `symfony/` directory create a `.env.local` file, which will be where you store your local environment variables you don't wish to be committed to your repository. For example, your database connection settings. Copy the following line into your `.env.local` file:

```
DATABASE_URL=postgresql://user:password@postgres:5432/test?serverVersion=11&charset=utf8
```

### Running Docker

Within the `docker/` directory run: `docker-compose up -d`.

Once completed should be shown the confirmation that the three containers are running.

### Running Database Migrations

In your terminal, connect to the bash prompt from within the PHP Docker container by running the following command:

```
docker-compose exec php bash
```

Then run to create the database tables by running the command below. Which will take all of the migration files found in `symfony/src/migrations/` and execute them. For this example it creates a user database table with the relevant columns.

```
php bin/console doctrine:migrations:migrate
```

### Test Run the Application

Go to: [http://localhost:8081/register/](http://localhost:8081/register) in your browser, you should be greeted with a registration form.

Enter a test telephone number and password. On submission of the form you should now be taken to the profile page!

If you're at this point, you're all set up and ready for this tutorial.

## Code of Conduct

In the interest of fostering an open and welcoming environment, we strive to make participation in our project and our community a harassment-free experience for everyone. Please check out our [Code of Conduct][coc] in full.

## Contributing
We :heart: contributions from everyone! Check out the [Contributing Guidelines][contributing] for more information.

[![contributions welcome][contribadge]][issues]

## License

This project is subject to the [MIT License][license]

[logo]: vonage_logo.png "Vonage"
[contribadge]: https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat "Contributions Welcome"

[coc]: CODE_OF_CONDUCT.md "Code of Conduct"
[contributing]: CONTRIBUTING.md "Contributing"
[license]: LICENSE "MIT License"

[issues]: ./../../issues "Issues"