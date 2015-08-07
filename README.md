#btcsumo [![Build Status](https://img.shields.io/travis/btcsumo/btcsumo.svg)](https://travis-ci.org/btcsumo/btcsumo) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/btcsumo/btcsumo.svg)](https://scrutinizer-ci.com/g/btcsumo/btcsumo/?branch=master)
Home of the http://btcsumo.com WordPress site, bringing all your Bitcoin news together.

This repository is basically the WordPress `content` folder, which contains the theme and necessary plugins.


##Installation

By following this installation, you won't need any XAMPP or MAMP installed!

A Vagrant virtual server with all necessary tools will be set up for you.

- Install [Virtual Box](https://www.virtualbox.org/wiki/Downloads)
- Install [Vagrant](http://www.vagrantup.com/downloads.html)
- Install [Node.js](https://nodejs.org/download/)
- Now open up that terminal app and enter the following commands (without the `$`).

- Make sure we have ownership of the vital npm folders, which may be necessary to not require root access to install node packages. Not doing this may break the node installation, so just do it :-p

        $ sudo chown -R $(whoami) /usr/local/lib/node_modules
        $ sudo chown -R $(whoami) ~/.npm

- Create a project folder wherever you want and change into it.

  **NOTE: This project folder will contain the *entire* WordPress installation.**

        $ mkdir <project>
        $ cd <project>

- Since we're going to use [Chassis](https://github.com/Chassis/Chassis) as our Vagrant virtual machine, we will get only the latest version of the master branch and add phpMyAdmin to it for easy database access.

        $ git clone --depth=1 --recursive https://github.com/Chassis/Chassis.git .
        $ git clone --depth=1 --recursive https://github.com/Chassis/phpMyAdmin.git extensions/phpmyadmin

- Now we fetch the content of this `btcsumo` repo, our actual theme and all necessary plugins.

        $ git clone https://github.com/btcsumo/btcsumo.git content

- Change into the theme folder, which is our main working area, and install all necessary build tools.

  *(this will take some time, depending on your internet connection speed)*

        $ cd content/themes/btcsumo
        $ npm install -g npm@latest
        $ npm install -g gulp bower
        $ npm install
        $ bower install

- After everything is set up, we start our virtual machine using Vagrant, which will need to download an image and make the virtual server installation first. Get prepared for a bit of a wait ;-)

  *(this will take some time, depending on your internet connection speed)*

        $ vagrant up

  *(if this should fail for some reason, destroy the virtual box with `$ vagrant destroy` and try again.)*

- Right, now our local development server can be accessed via **http://btcsumo.local** :-D

- If you couldn't wait and wanted to test the site, you might have noticed that it has no CSS loaded.
  That's because we need to build the initial project first!

        $ gulp build

- Now whenever you're working on the project, simply run the following command from inside your theme folder (the one we're still in right now).

        $ gulp watch

  This will open a page in your browser and watch the folder for changes. All changes get injected into your site automatically, no need to refresh the page!

**Is this cool or what?!**

To install the initial feeds, go here: http://btcsumo.local/content/themes/btcsumo/add-initial-feeds.php

Now you should see some content when you refresh the home page.


## Logging in

**WP Admin**
- url: http://btcsumo.local/wp-admin
- usr: sumo
- pwd: sumo

**phpMyAdmin**
- url: http://btcsumo.local/phpmyadmin
- No login required.

**SSH into vagrant server**
(from anywhere within your project folder)

    $ vagrant ssh

## Info

Current Sage commit, to know what to compare to when updating Sage:
https://github.com/roots/sage/tree/12fe0473683553f2adf7652076b198ce122c5cc1