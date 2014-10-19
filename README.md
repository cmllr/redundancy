Redundancy  1.9.15 branch
=================================
[![Build Status](https://travis-ci.org/squarerootfury/redundancy.svg?branch=Lenticularis)](https://travis-ci.org/squarerootfury/redundancy)

> Please note: This branch is _only_ for developing purposes. This version is bleeding edge, instable, uncomplete and does not have all functions

Redundancy is an lightweight cloud computing system. The program is so lightweight that you can run it on microservers like the Raspberry Pi without having too much load.
Redundancy does not require full server access. It can be installed on every server running a webserver using PHP. The configuration is very easy through a central configuration file. At the moment it does not have a "bling bling" configuration wizard. The stable branch will probably get an installer and an installation wizard, too. The biggest difference between Redundancy and other programs for this purpose is that you get a very lightweight user
experience. There are no unnecessary features you probably never use. Redundancy focuses on the core of the task to create an easy cloud, for example to use at home.

Requirements
------------

for server:
- PHP 5.5.x is recommended
- PHP GD modules
- PHP zip modules
- MySQL(i) or equivalent

for client:
- Javascript support

License and components
----------------------

- Redundancy is licensed under the terms and conditions of the GNU GPL v3.
- Redundancy uses JQuery, it can be found on https://jquery.org/.
- Redundancy uses parts of Faenza, it can be found on http://gnome-look.org/content/show.php?content=128143
- Redundancy uses Twitter Bootstrap, licensed under the terms and conditions of Apache License 2.0
- Redundancy uses webfont Elusive (http://shoestrap.org/downloads/elusive-icons-webfont/), licensed under the terms and conditions of the SIL Open Font License (OFL)
- Redundancy uses an image from http://subtlepatterns.com/

Note/ Disclaimer
----------------

Redundancy's default branch is unstable. At the moment there is no stable version available. The program runs on every configured server, but the possibility of loosing data caused by hidden bugs is very high. Please feel free to inform me about these issues over the github issue tracker. Thanks :).

Since 1.9.7 it is possible to create snapshots. It will be recommended (if the feature is out of beta state) to run these snapshots via cronjobs or tasks cyclic to avoid data loss.
