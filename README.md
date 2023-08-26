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

### Improvements
In order to prevent race conditions on the booking system, the script works properly, because it is only a single DB query being executed and it is locked until the process is finished.
But if we add something complex to the logic, we have to deal with it.

An option is, we can Set the number of filled slots in the ``TimeSlot`` table and whenever a user starts to book, we use Redis to add the number of people in the process of booking.

Let's say a time slot has 5 capacity, 3 of them are already filled and 3 people are trying to book the remaining 2 slots at the same time.

1. The first one gets started and we set the Redis key ``TimeSlot{ID}`` to ``1``.
2. The second one starts the process and calculates all possible empty places: 3 are already booked and one in ``TimeSlot{ID}`` Redis key, so there is still an empty place left. It starts the process and increments the value of ``TimeSlot{ID}``.
3. The third one starts the process. The empty places are now 3 already booked + 2 in booking progress in Redis = 5, so there is no space left, so he/she gets an error.
4. Whenever each user finished the booking process, the key ``TimeSlot{ID}`` gets decreased.

Because Redis uses memory and it is super fast, this method can solve any race conditions, but I did not implement that in this context. 
