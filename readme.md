JaztecAcl
=========
[![Build Status](https://travis-ci.org/jaztec/jaztec-acl.svg?branch=master)](https://travis-ci.org/jaztec/jaztec-acl)

Simple database driven ACL module for ZF2.

## Installation

You can install the module with composer using

```sh
./composer.phar require jaztec/jaztec-acl
```

or include it in the composer.json of your project with

```sh
{
    "require": {
        "jaztec/jaztec-base": "0.1.*",
        "jaztec/jaztec-acl": "0.1.*"
    }
}

```

After installation make sure your database setup is completed.
For a clean install run:
```sh
./php public/index.php acl database clean-install --email=[your_email] [--verbose|-v]
```
 Or, after updating the source code:
```sh
[site_root]./php public/index.php acl database update [--verbose|-v]
```

Congratulations, the ACL module has been installed. If you performed a clean install a user will have been added with username 'admin' and password 'admin'

For any help using the console functions
```sh
[site_root]./php public/index.php acl database --help
```