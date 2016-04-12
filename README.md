# Monitoring Traffic Office - Beritagar

### Version
0.1

### Tech

Dillinger uses a number of open source projects to work properly:

* [Twig](http://twig.sensiolabs.org) - HTML template for web apps!
* [nodeJS](http://nodejs.org) - socket I/O for the backend
* [PHP](http://php.net) - using Slim Framework
* [jQuery] - javascript library

And itself is open source with a [public repository](https://github.com/beritagarid/monitoring-traffic-office)
 on GitHub.

### Configuration
Global configuration path : 
```sh
$ cp apps/config/config.sample.php apps/config/config.php
```
### Installation

You need Forever installed globally:

```sh
$ npm i -g forever
```

```sh
$ git clone [git-repo-url] monitoring-traffic-office
$ cd monitoring-traffic-office
$ composer install
```

```sh
$ php server.php
```

### MikroTik
#### Address List
```sh
/system scheduler 
add name="Update Nice" start-date=mar/24/2016 start-time=05:27:43 interval=1d on-event=:if ([:len [/file find name=nice.rsc]] > 0) do={/file remove nice.rsc }; /tool fetch address=ixp.mikrotik.co.id src-path=/download/nice.rsc mode=http;/import nice.rsc policy=ftp,reboot,read,write,policy,test,password,sniff,sensitive 
```

#### Mangle
```sh
/ip fi ma
add action=mark-connection chain=forward comment=IX dst-address-list=!nice in-interface=[local-interface] new-connection-mark=internasional.conn out-interface=[interface-wan]
add action=mark-connection chain=forward in-interface=[interface-wan] new-connection-mark=internasional.conn out-interface=[local-interface] src-address-list=!nice

add action=jump chain=prerouting dst-address-list=!nice jump-target=IX.pre src-address=[local-prefix] add action=mark-packet chain=IX.pre connection-mark=internasional.conn new-packet-mark=IX passthrough=no
add chain=IX.pre action=mark-packet new-packet-mark=IX passthrough=no connection-mark=internasional.conn log=no log-prefix=""

add action=jump chain=postrouting dst-address=[local-prefix] jump-target=IX.post src-address-list=!nice add action=mark-packet chain=IX.post connection-mark=internasional.conn new-packet-mark=IX passthrough=no
add chain=IX.post action=mark-packet new-packet-mark=IX passthrough=no connection-mark=internasional.conn log=no log-prefix=""

add action=mark-connection chain=forward comment=OpenIXP dst-address-list=nice in-interface=[local-interface] new-connection-mark=oix.conn out-interface=[interface-wan]
add action=mark-connection chain=forward in-interface=[interface-wan] new-connection-mark=oix.conn out-interface=[local-interface] src-address-list=nice

add action=jump chain=prerouting dst-address-list=nice jump-target=OIX.pre src-address=[local-prefix] add action=mark-packet chain=OIX.pre connection-mark=oix.conn new-packet-mark=OIX passthrough=no
add action=mark-packet chain=OIX.pre connection-mark=oix.conn new-packet-mark=OIX passthrough=no

add action=jump chain=postrouting dst-address=[local-prefix] jump-target=OIX.post src-address-list=nice
add action=mark-packet chain=OIX.post connection-mark=oix.conn new-packet-mark=OIX passthrough=no
```

`[local-prefix]` = Your local subnet/prefix (eg. 192.168.100.0/24)

`[local-interface]` = The interface where your client connected to.

`[interface-wan]` = The gateway interface