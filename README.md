# WordPress Ajax load more posts

## Description

A WordPress plugin to load more posts via ajax.
- This plugin overwrites the `get_the_posts_navigation` function. So you don't need any shortcodes.
- This plugins uses no dependencies, is translatable and WPML ready. 
- The default previous and next links that are generated via `get_the_posts_navigation` keep available for visitors with JavaScript disabled.

This plugin just grabs the next page url, makes an Ajax request to that url and parses it. Therefore you don't need to worry if your theme templates and functionality gets used. What works for a browser request, works for the ajax request. The new posts get inserted to a container with the class `posts`. Make sure you have that class on your container or overwrite the selector via the `load-more-posts-js-vars` filter

## Motivation

I build a lot of sites that needed a functionality like this and decided to create a plugin for it. Although there are a lot of plugins doing something like this, they usually add a lot of bloat and are not developer friendly. This plugin is for a developer easier to implement, easier to edit and keeps te codebase cleaner.

## Installation

Clone this repo to your plugins or mu-plugins folder. When you load it in your mu-plugins folder, you have to call the plugin via a file that is directly in the `mu-plugins` folder. See [this article](https://www.sitepoint.com/wordpress-mu-plugins/) for more information.

## License

GNU GENERAL PUBLIC LICENSE
