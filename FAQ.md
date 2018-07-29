# Frequently Asked Questions

## Is this just the WP API Menus plugin?

This is a fork of that plugin. I needed some slightly different formatting returned and that plugin is no longer maintained. In addition to the slightly different formatting, I also added support for menu items that are custom items, categories, and taxonomies. I also added unit tests to ensure future changes don't break things. Props to 


## Does this plugin support the v1 REST API?

No. That's not included in core, so there's no reason to support it.


## Why are there two versions of the classes for menu items?

I prefer to use the npm classnames utility package to help manage class names, so I needed the class names returned as an array. But the original plugin returned everything as a string, so I wanted to offer that as well. There's no reason not to do both, so that's what I did here.


## What about CORS headers?

This plugin opens up the CORS headers for the menus for GET requests, but nothing else.