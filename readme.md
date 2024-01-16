# First run

Start docker container
```shell
docker compose up -d
```

Run migrations
```shell
 docker exec -it api_backend php bin/console doctrine:migrations:migrate
```

# Usage
```shell
 docker exec -it api_frontend php bin/console app [command]
```

Available commands:
```
app:group:create       Creates a new group.
app:group:delete       Deletes a group.
app:group:list         Get groups list.
app:group:report       Get groups report.
app:group:update       Updates a group.
app:user:add-to-group  Add user to the group.
app:user:create        Creates a new user.
app:user:delete        Deletes a user.
app:user:list          Get users list.
app:user:update        Updates a user.
```
