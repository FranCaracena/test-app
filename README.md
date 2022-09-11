## Installation requirements

Dependencies

- PHP ^8.1
- MariaDB ^10.6

## Installation guide

- Copy or rename file .env.example to .env
- Create a database for the application(default name is 'test_app', but can be changed in the .env file)
- Open a terminal and execute ```composer install```
- Execute ```php artisan key:generate```
- Execute ```php artisan migrate```
- Execute ```php artisan db:seed```

## API Routes and params

Params marked with an asterisk are optional

| Operation | Route | Method | Params |
|---|---|---|---|
| Create Club |http://test-app.localhost/api/club/new | POST | name <br /> budget  |
| Add coach to club |http://test-app.localhost/api/club/new-coach | POST | club_id <br /> coach_id |
| Add player to club | http://test-app.localhost/api/club/new-player | POST | club_id <br /> player_id |
| Remove player from club | http://test-app.localhost/api/club/player/remove | POST | club_id <br /> player_id |
| Remove coach from club | http://test-app.localhost/api/club/coach/remove | POST | club_id |
| Update club budget | http://test-app.localhost/api/club/change-budget | POST | club_id <br /> budget |
| List players in a club | http://test-app.localhost/api/club/list-players | GET | club_id <br /> * page <br /> * id <br /> * name <br /> * email |
| Create a player | http://test-app.localhost/api/player/new | POST | name <br /> email <br /> salary |
| Create coach | http://test-app.localhost/api/coach/new | POST | name <br /> email <br /> salary |
