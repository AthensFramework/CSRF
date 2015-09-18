[![Build Status](https://travis-ci.org/UWEnrollmentManagement/CSRF.svg?branch=master)](https://travis-ci.org/UWEnrollmentManagement/CSRF)
[![Code Climate](https://codeclimate.com/github/UWEnrollmentManagement/CSRF/badges/gpa.svg)](https://codeclimate.com/github/UWEnrollmentManagement/CSRF)
[![Test Coverage](https://codeclimate.com/github/UWEnrollmentManagement/CSRF/badges/coverage.svg)](https://codeclimate.com/github/UWEnrollmentManagement/CSRF/coverage)

UWDOEM/CSRF
=============

Easily protect against CSRF attacks.


Installation
===============

This library is published on packagist. To install using Composer, add the `"uwdoem/csrf": "0.1.*"` line to your "require" dependencies:

```
{
    "require": {
        "uwdoem/csrf": ">=0.1"
    }
}
```

Of course, if you're not using Composer then you can download the repository using the *Download ZIP* button at right.

Use
===

Using this package requires only two lines:
```
    // Import the CSRF class
    use UWDOEM\CSRF\CSRF;
    
    // Intialize
    CSRF::init();
```

The method `::init()` will automatically insert a hidden CSRF token field into your forms:
```
    <!--output html-->
    ...
    <form>
        <input type=hidden name=csrf_token value=37328bc2cac3e73623bc38ab0f4068ee7fa1>
    ...
```

Incase you perform form submission via AJAX, `::init()` also inserts a `CSRF_TOKEN` variable into your javascript:
```
    <!--output html-->
    ...
    <head>
        <script>var CSRFTOKEN = '37328bc2cac3e73623bc38ab0f4068ee7fa1';</script>
    ...
```

Troubleshooting
===============
 
 Instructions to follow.
 
Compatibility
=============

* PHP 5.5, 5.6, 7.0

Todo
====

See GitHub [issue tracker](https://github.com/UWEnrollmentManagement/CSRF/issues/).

License
====

Employees of the University of Washington may use this code in any capacity, without reservation.

Getting Involved
================

Feel free to open pull requests or issues. [GitHub](https://github.com/UWEnrollmentManagement/CSRF) is the canonical location of this project.
