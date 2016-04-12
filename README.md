# Monitoring Traffic Office - Beritagar
## Table of contents
1. [Version] (#Version)
2. [Requirements] (#Requirements)
3. [Configuration] (#Configuration)
4. [Installation] (#Installation)
5. [Mikrotik] (#Mikrotik)

### Version
0.1

### Requirements
* NodeJS 
* NPM Forever
* PHP >= 5.5
* [Composer] (https://getcomposer.org)

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
$ git clone https://github.com/beritagarid/monitoring-traffic-office.git
$ cd monitoring-traffic-office
$ composer install
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

#### Simple Queue
We're using prefix {IX|OIX} to split Indonesia (OIX) and International (IX) bandwidth.on simple queue, for example `{IX|OIX}-Traffic` is a group and parent for another child. You can add the other queue below `{IX|OIX}-Traffic`. 

*Example:*

DIAGRAM
```
{IX|OIX}-TRAFFIC
   {IX|OIX}-SALES
       {IX|OIX}-YOURSALESNAME
```

CLI
```
/queue simple
add name="{IX|OIX}-TRAFFIC" target=[local-prefix] parent=none packet-marks=IX priority=1/1 queue=default-small/default-small limit-at=0/0 max-limit=10M/22M burst-limit=0/0 burst-threshold=0/0  burst-time=0s/0s 
add name="{IX|OIX}-SALES" target=[local-prefix] parent=IX-TRAFFIC packet-marks="" priority=8/8 queue=default-small/default-small limit-at=0/0 max-limit=3M/5M burst-limit=0/0 burst-threshold=0/0 burst-time=0s/0s
add name="{IX|OIX}-YOURSALESNAME" target=[ip-address]/32 parent={IX|OIX}-SALES packet-marks="" priority=8/* queue=default-small/default-small limit-at=0/0 max-limit=3M/5M burst-limit=0/0 burst-threshold=0/0 burst-time=0s/0s 
```

Don't forget to add the `IX` and `OIX` string, the script are parsing the data from that string.
