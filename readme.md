
## When should you use WPDB Migrate URLs?
If you **can not access the backend of your wordpress** because it has your http://old.domain and you need a **secure and easy way** to update your database to your http://new.domain this library is for you.

**Use this lib when "wp-migrate-db" plugin in is not an alternative.**

## Short description
Migrating a Wordpress theme to a new host shouldn't be a pain in the ass, that's why I created this library to help myself and everyone else who needs to migrate a wordpress in seconds without any issues.

**Heads up!** Before you start remember to backup your database. If you lose anything It will be an Epic Facepalm because of your laziness.

# Wordpress Database Migrate URLs

WPDB Migrate URLs is a **front end solution** created to easily migrate urls and [serialized php][1] data from your www.old.domain to your www.new.domain.

## Features

- Replace URLs from one domain to another Fast and Easy.
- Access to the FrontEnd panel from a custom url for securty purposes.
- FrontEnd button to remove the library access after usage to avoid security issues.
- Serialized PHP and Revolution Slider Support! :D

## Instalation and Usage

Since you cannot access your */wp-admin* when your database still has the *old.domain*, you will have to install this library in your theme folder rather than plugins folder.

    …/wp-content/themes/myTheme/lib/wpdb-migrate-urls-x.x.x

Simple `require_once` the file **wpdb-migrate-urls.php** after the body tag of your wordpress theme. Note that you will have to change x.x.x to the current version.

Code Example: 

```<body>
      <?php 
      $wpdb_mu_relative_path = "path/to/wpdb-migrate-urls/wpdb-migrate-urls.php";
      if(file_exists(dirname( __FILE__ ) . "/$wpdb_mu_relative_path")) require_once("$wpdb_mu_relative_path"); ?>
    …
```


Now you will be able to see a form under anypage if you add "**?wpdb_migrate**" at the end of your url. Ex. "www.new.domain/**?wpdb_migrate**".

Just fill the input with your new url and click "Migrate Database URLs!".

That's It! Fast an easy as It should be :)

## How it works?

The library use MySQL to change every www.old.domain to your www.new.domain under the tables:

    wp_options, wp_post, wp_postmeta, wp_revslider_slides and wp_revslider_static_slides.

**Note:** "wp_" also known as "wordpress database prefix" is retrieved dynamically by the library functions so you won't have any issues if you have changed it on your installation to something custom.

---

If you have any issues or questions don't hesitate to write me and I will do my best to help you and improve this library.


[1]: http://php.net/manual/es/function.serialize.php


