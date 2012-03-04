Routes
==========

Proposed RESTful URL patterns:
---------------------------------

```
.json extensions should be supported for Ajax requests
(consider .xml if more appropriate for cake framework)

GET /users                                                       (lists all users)
GET /users/view/<user_id>                                        (lists the details of a specific user)

GET /species                                                     (lists the known species)
GET /species/view/<species_id>                                   (lists the details of a specific species)
GET /species/occurrences/<species_id>                            (lists all occurrences of a given a species)

GET /occurrences                                                 (lists all occurrences)
GET /occurrences/<occurence_id>                                  (lists the details of a specific occurrence)
```

*N.B.* These may change based on PHP Cake conventions

Setup
==========

php.ini
---------

To allow for large species file uploads, it may be necessary to increase <code>max_execution_time</code>.
In my case, it took approximately 1 minute to upload and process a species with 130k occurrences.
I have set <code>max_execution_time</code> to 300, to be on the safe side.

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
