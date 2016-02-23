[![Build Status](https://travis-ci.org/AthensFramework/CSRF.svg?branch=master)](https://travis-ci.org/AthensFramework/CSRF)
[![Code Climate](https://codeclimate.com/github/AthensFramework/CSRF/badges/gpa.svg)](https://codeclimate.com/github/AthensFramework/CSRF)
[![Test Coverage](https://codeclimate.com/github/AthensFramework/CSRF/badges/coverage.svg)](https://codeclimate.com/github/AthensFramework/CSRF/coverage)
[![Latest Stable Version](https://poser.pugx.org/athens/csrf/v/stable)](https://packagist.org/packages/athens/csrf)

Athens/CSRF
=============

Easily protect against [CSRF](https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)) attacks.


Installation
------------

This library is published on packagist. To install using Composer, add the `"athens/csrf": "0.1.*"` line to your "require" dependencies:

```
{
    "require": {
        ...
        "athens/csrf": "1.*",
        ...
    }
}
```

Of course, if you're not using Composer then you can download the repository using the *Download ZIP* button at right.

Use
---

Using this package requires only two lines:
```
    // Import the CSRF class
    use Athens\CSRF;
    
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
This token will be included automatically in any of your form submissions.

Incase you perform form submission via AJAX, `::init()` also inserts a `CSRF_TOKEN` variable into your javascript:
```
    <!--output html-->
    ...
    <head>
        <script>var CSRFTOKEN = '37328bc2cac3e73623bc38ab0f4068ee7fa1';</script>
    ...
```
This token will not automatically be included in your AJAX requests, but you may include it manually by referring to the `CSRFTOKEN` var in your submission script.
 
Compatibility
-------------

* PHP 5.5, 5.6, 7.0

Todo
----

See GitHub [issue tracker](https://github.com/AthensFramework/CSRF/issues/).


Getting Involved
----------------

Feel free to open pull requests or issues. [GitHub](https://github.com/AthensFramework/CSRF) is the canonical location of this project.

Here's the general sequence of events for code contribution:

1. Open an issue in the [issue tracker](https://github.com/AthensFramework/CSRF/issues/).
2. In any order:
  * Submit a pull request with a **failing** test that demonstrates the issue/feature.
  * Get acknowledgement/concurrence.
3. Revise your pull request to pass the test in (2). Include documentation, if appropriate.
