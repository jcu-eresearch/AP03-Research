Routes
==========

Proposed RESTful URL patterns:
---------------------------------

```
.json extensions should be supported for Ajax requests
(consider .xml if more appropriate for cake framework)

GET /users                                                       (lists all users)
GET /users/<user_id>                                             (lists the details of a specific user)

GET /species                                                     (lists the known species)
GET /species/<species_id>                                        (lists the details of a specific species)
GET /species/<species_id>/occurrences                            (lists all occurrences of a given a species)
GET /species/<species_id>/occurrences/<occurence_id>             (lists the details of a specific occurrence)
```

*N.B.* These may change based on PHP Cake conventions

Setup
==========

DB
---------

```sql

CREATE TABLE species (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);

CREATE TABLE occurrences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    species_id INT UNSIGNED,
    latitude DECIMAL(12,9) NOT NULL,
    longitude DECIMAL(12,9) NOT NULL,
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);

```
