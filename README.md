# FCL Authentication in PHP

A sample repository that shows how to authenticate with FCL
wallets from a PHP application.

> :warning: The code in this repository is not meant for production use and only serves as an example for developers working with FCL in PHP applications. 

## Full example

The [Main.php](src/Main.php) file contains a complete end-to-end example of the authentication process.

## Getting started

```sh
composer i
```

```sh
export FCL_AUTHN_URL=https://flow-wallet.blocto.app/api/flow/authn
export FCL_HTTP_REFERER=https://foo.com

# Run the sample code in src/Main.php
composer run-script main
```

## Run tests

```sh
composer run-script tests
```
