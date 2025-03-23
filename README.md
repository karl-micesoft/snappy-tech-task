# Snappy Tech Task

## Background
This solution to the tech task has been written using Laravel. While I could have completed the task more
efficiently using a different method, I decided to use this opportunity to refresh my knowledge on Laravel.

### Docker

Docker compose configuration has been included for ease of development and testing. To bring this up, use:
```
docker compose -f compose.dev.yaml up -d
```

To help run artisan commands, a "workspace" container and small shell script have been included.
To invoke commands inside the workspace, use this `ws` script, e.g.
```
./ws php artisan ...
```

## Console import command
An artisan console command (postcode:import) was written to download postcode data from the example URL
provided and import it into a database table. This has been built out using a simple interface and a
specific implementation, which is then configured in the service provider. This will allow new implementations
to be added easily.

There are some cleanup tasks missing from this implementation, which would close files and delete temporary
files.

## Public APIs
```
GET /stores
GET /stores/nearby?postcode=AA999AA
GET /stores/deliver?postcode=AA999AA
```

These return a list of stores. The first returns all stores.
The second returns them sorted by distance from postcode.
The third returns those which will deliver to the postcode.

No pagination is currently implemented, as it is expected that this initial exercise would be run with
only a small number of stores.

All distances are in meters.

## Private API
```
POST /stores
```

This accepts a body as shown below, and will create a new store in the DB.
```json
{
    "name": "Name of store",
    "latitude": "58.1234",
    "longitude": "-1.259",
    "open": true,
    "store_type": "store",
    "max_delivery_distance": 1000.00
}
```

This requires a valid JWT Bearer token in the Authorization header. This has been implemented in a basic manner to just check for a valid, non-expired JWT
but it would be expected that more checks would be performed.

Please remember all distances are in meters.

For testing purposes, there is an artisan command to generate valid or expired tokens:
```
./ws php artisan jwt:issue [--expired]
```

## Further steps

This code would require integration into the existing application, as well as for far more tests to be written.
Pagination would be a must, as would further checks on the JWT (or other auth method).
