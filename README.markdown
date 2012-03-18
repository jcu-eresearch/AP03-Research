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

The following are the changes I have made to the php.ini file.

```php

; max_execution_time = 30
max_execution_time = 300


; memory_limit = 128M
memory_limit = 1024M


; error_reporting = E_ALL & ~E_DEPRECATED
error_reporting = E_ALL | STRICT


; upload_max_filesize = 2M
upload_max_filesize = 20M


;post_max_size = 8M
post_max_size = 20M


; date.timezone =
date.timezone = 'Australia/Brisbane'

```

To allow for large species file uploads, it may be necessary to increase both the <code>max_execution_time</code> and the <code>upload_max_filesize</code>.
In my case, it took approximately 4 minutes to upload and process a json formatted file containing a species and its approx 130k occurrences (a 4MB file).

The generation of the geoJSON cluster information consumes large amounts of memory. It is *very* likely that this could be opmitimized.
For now, increase the <code>memory_limit</code> to at least 512M. I have increased it to 1024M to be on the safe side.

I have increased my <code>error_reporting</code>. You don't need to do this to run the applcication.

I have set <code>date.timezone</code>. Without this being set, cakephp produced a significant number of date related warnings.


DB
---------

mysql
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

sqlite
---------

```sql

CREATE TABLE species (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);

CREATE TABLE occurrences (
    id INTEGER PRIMARY KEY,
    species_id INT UNSIGNED,
    latitude DECIMAL(12,9) NOT NULL,
    longitude DECIMAL(12,9) NOT NULL,
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);

```
