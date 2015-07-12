Converge
========

NOTE: Not usable. Not a single feature is usable. Stop looking.

Collaboration and Communication platform. Currently mostly for discussions. If I would add the buttons for posting things.

It also provides some other fun, like:

 * task management (rudimentary)
 * activity streams (rudimentary, to be done)
 * content publishing (rudimentary, to be done)
 * converging all other kinds of communications, like IM and email (to be done)

See? We are disruptive. Innovative. What do you want more? A finished app?

**OMGz!**

Requirements
============

* PHP 5.4 (should also work fine on HHVM)
  * PDO
* (currently) MySql

Installation
============

Application
-----------

1. Install dependencies with Composer
1. Run the `scripts/install.php` with your favorite PHP distribution. It will also set up the first user.
  * If you want, also run `seed_default_tags.php` and `seed_task_properties.php` from the directory.
1. Have the `/cache/` directory writable (by whoever runs the following command + who runs PHP in the web server context)
1. Run `composer rsrc:compile` to compile all static resources (LESS, JavaScript [not minified yet])

Webserver
---------

* Point your webserver at `/webroot/` (for old PHP-generated HTML) or `/angular/` (for experimental and feature-incomple Angular frontend)

For `/webroot/` variant:

* Additionally configure your webserver to serve all non-files from `app.php`. You don't need to pass the original URL to the application, as long as your webserver is configured correctly with PHP.

License
=======

Released under Apache v2.0.

Contributing
============

When you contributing you have to accept the Contributor License Agreement.

* You forfeit all legal ownership claims and rights to your contributed content
* You assure that the contributed content is either
    * free of any license legalities (as in, free for us to use and derive)
    * or contains the necessary permissions for us to use it both commercially and non-commercially, including any applicable patent licenses
* You can not pull back the contributed content - not because we are evil corporate lawyers, but see above
* You are still guaranteed credit where credit is due (usually your name and email as commit authorship attribute)
* You can still say "I did this!" for bragging rights only
