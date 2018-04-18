# auth-abstractor

A PHP library which aims to completely abstract the authentication logic
from your PHP web application. You won't even have to create the views!

It does so by acting as a middleware. Simply pass it the HTTP request and you
will get back an HTTP response along with an object of the class
AuthenticationProcess.

## Installation

### Assets

In order for U2F registration and authentication to work, you will need
[google-u2f-api.js](https://www.npmjs.com/package/google-u2f-api.js) and
[jquery](https://www.npmjs.com/package/jquery). These files need to be in the
folder which path is given by [getAssetUri()](https://github.com/matthewslouismarie/auth-abstractor/blob/a97f0a64d5f0f8760d133f34afcf2a44ab1aa082/src/LM/Authentifier/Configuration/IApplicationConfiguration.php#L11).

Of course, you can override the U2F views with your very own views which can
use different JavaScript libraries.

## Configuration

AuthenticationKernel is the entry point of _auth-abstractor_. To use any of the
features of _auth-abstractor_, you first need to initialise
[AuthenticationKernel](https://github.com/matthewslouismarie/auth-abstractor/blob/master/src/LM/Authentifier/Controller/AuthenticationKernel.php) with an implementation of
[IApplicationConfiguration](https://github.com/matthewslouismarie/auth-abstractor/blob/master/src/LM/Authentifier/Configuration/IApplicationConfiguration.php). AuthenticationKernel is an object that can be
common to your entire web applications, so you can register it as a service if
your web application supports dependency injection (e.g. Symfony).

## Handling the Authentication Process

_Authentication process_ corresponds to HTTP cycle for the user to authenticate.
This can be logging in (entering the username and password) or proving the
user's identity when the user is already logged in (e.g. entering the password)
before performing a secure operation, such as transferring money. It can also be
registering.

Let's suppose you have an HTTP controller for your login page. Your controller
will be responsible for:

1. Initialise a new AuthenticationProcess object, e.g. when the user arrives
on the login page, or retrieve it (e.g. from the session). When you initialise
an AuthenticationProcess object, you need to pass certain parameters such as the
_challenges_ (e.g. ask for password, U2F device, etc.) and a callback that will
be executed when the authentication fails or succeeds.
2. Pass the AuthenticationProcess object to the _processHttpRequest()_ method of
your AuthenticationKernel object, along with the PSR-7 HTTP request.
3. Store the AuthenticationProcess object returned by _processHttpRequest()_
(e.g. in session) and send the HTTP response to the user.
4. When the _authentication process_ succeeds (the user proved their identity)
or fails (the user tried too many attempts), your callback is called and will
return an HTTP response, which _processHttpRequest()_ will return back to the
controller.