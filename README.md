# Monitoring Traffic Office - Beritagar

### Version
0.1

### Tech

Dillinger uses a number of open source projects to work properly:

* [Twig](http://twig.sensiolabs.org) - HTML template for web apps!
* [nodeJS](http://nodejs.org) - socket I/O for the backend
* [PHP](http://php.net) - using Slim Framework
* [jQuery](https://jquery.com) - javascript library

And itself is open source with a [public repository](https://github.com/beritagarid/monitoring-traffic-office)
 on GitHub.


### Installation

You need Forever installed globally:

```sh
$ npm i -g forever
```
```sh
$ git clone https://github.com/beritagarid/monitoring-traffic-office
$ cd monitoring-traffic-office
$ composer install
```
### Configuration
Global configuration path : 
```sh
$ cp apps/config/config.sample.php apps/config/config.php
```
### Runnning

```sh
$ php server.php
```
