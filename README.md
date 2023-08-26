<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Laravel simple escape room booking

This project is a simple escape room booking system. It is created in 7 hours in total.

### Features
- Escape rooms with different themes and time slots
- Ability to book a time slot
- Automatically apply a 10% discount on user's birthday

### Requirements
You need to have [Docker](https://www.docker.com/) and [Docker-compose](https://docs.docker.com/compose/) installed on your system.

### Deployment & running
1. Clone the repository to your computer
2. Go to the application directory and run the following code in Terminal/Command prompt:
   ``docker-compose up -d``.
3. Copy ``.env.example`` file to ``.env`` and edit the file with following data:
    ```
   DB_HOST=db
   DB_PASSWORD=102030
   ```
4. Now you should initialize the Laravel application. Just copy and paste the following commands:
    ```
   docker-compose exec app composer update
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   ```
5. After everything is ready, you can open the project by navigating to ``https://localhost`` in your browser.
6. To test the application, you can run the following command:
    ```
   docker-compose exec app php artisan test
   ```
   And to test with coverage report, you can simply run
    ```
    docker-compose exec app php artisan test --coverage-html coverage
   ```
   and check the coverage in ``coverage/`` folder. Currently, all controller methods are covered by tests.
