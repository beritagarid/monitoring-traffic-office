# Monitoring Traffic Office - Beritagar

Realtime bandwidth monitoring tool using PHP,Socket.io and MikroTik RouterOS API protocol.

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

```sh
$ git clone https://github.com/beritagarid/monitoring-traffic-office
$ cd monitoring-traffic-office
$ composer install
```

Install npm package on folder `socket`

```sh
$ cd socket/  && npm install
```

You need Forever installed globally:

```sh
$ npm i -g forever
```

### Configuration

Global configuration path:

```sh
$ cp apps/config/config.sample.php apps/config/config.php
```

### Runnning 

Backend:

```sh
$  forever start -c php runner/server.php
```
Backend Node:

```sh
$  forever start socket/index.js
```

Front End:

```sh
$ php server.php
```
