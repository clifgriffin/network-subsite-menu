Network Subsite Menu
====================

Posted in case anyone finds it useful.  This is definitely a scratch your own niche (get it??) kind of plugin.

Basically, if you have a multisite and you want to create a global menu with some or all of your subsites and show which subsite you're on, this will help you build that menu. 

To use it, Network Activate it and then go to Settings -> Network Menu.

You'll see a list of your subsites, with a checkbox to enable or disable that subsite in the menu. There is also a field to set the menu label. 

## Implementation

Once you've configured it, just drop this somewhere:
```php
wp_nav_menu( array('menu' => 'network_subsite_menu') );
```

In reality, the plugin doesn't really use `wp_nav_menu`, so you're going to be pretty dissappointed if you try to pass other options.  It's building out the menu manually, using the standard WordPress nav menu HTML and then circumventing the whole nav menu function by directly returning the output.

The only parameter that will work is `echo`.

## Filters

If you need anything beyond the basics, these filters should get you there in theory:

### cgd_network_subsite_menu_settings

Filter the whole settings object.  You could change the order of menu items here. 

### cgd_network_subsite_menu_before

Lets you insert menu items before the main menu item loop. 

### cgd_network_subsite_menu_after

Lets you insert menu items after the main menu item loop. 

### cgd_network_subsite_menu_site_info

Filter the url, label, and set whether or not the current menu item is the current one. 

To see how these actually work, look at the code dawg. 

