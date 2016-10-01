# InterVarsity WordPress Plugin

*Copyright 2016 Caleb Evans*  
*Released under the GNU General Public License v2.0*

The InterVarsity Plugin is a WordPress plugin intended for [InterVarsity
Christian Fellowship/USA](http://intervarsity.org/) chapters with a WordPress
site. I originally created the plugin for the [InterVarsity North County chapter
website](http://ivnorthcounty.org/), but I have since decided to open-source the
plugin for the benefit of all InterVarsity chapters. Please note that this is an
unofficial plugin which is not affiliated with InterVarsity.

This plugin integrates perfectly with the [InterVarsity
Theme](https://github.com/caleb531/intervarsity-theme), which I also created for
the InterVarsity North County website. The theme is also open-source, and you
are certainly free to build your own WordPress theme that integrates with this
plugin.

## Requirements

1. Your site must be running WordPress 4.4 or newer.

2. You must install and activate the [Awesome
CPT](https://github.com/caleb531/awesome-cpt) plugin, as this is required for
the InterVarsity Plugin to function.

## Features

### Small group management

The InterVarsity Plugin adds a new section to the WordPress admin sidebar titled
**Small Groups**. In this screen, you can manage small groups just like you can
pages or posts, and you are able to perform the same basic operations (add,
view, edit, trash/delete).

#### What's in a small group?

Every small group has the standard title, description, and support for featured
images (post thumbnails). This plugin also adds several other fields specific to
small groups:

1. Details
  1. Time
  2. Start Date
  3. Location
  4. Leaders
2. Contact information
  1. Name
  2. Phone number
  3. Email address

Each small group can be assigned a campus to which it belongs. You can create
new campuses under the **Campuses** section of the **Small Groups** admin menu
in the sidebar. While you can technically assign multiple campuses to a single
small group, I recommend that each small group have only one assigned campus.

Similarly, you can create and assign any number of categories to any small
group. You can create new categories under the **Categories** section of the
**Small Groups** admin menu in the sidebar. Unlike campuses, it is acceptable
and encouraged to assign multiple categories to each small group as appropriate.

### Small group shortcodes

This plugin provides a number of
[shortcodes](https://codex.wordpress.org/Shortcode) which you can use to
retrieve information for a particular small group. These are most useful when
you wish to mention who's leading (for example) in the small group description.
The following shortcodes are available:

- `[sg-time]`
- `[sg-start-date]`
- `[sg-location]`
- `[sg-leaders]`
- `[sg-contact-name]`
- `[sg-contact-phone]`
- `[sg-contact-email]`

### Facebook Like Button shortcode

The plugin also includes a useful shortcode for adding a [Facebook Like
Button](https://developers.facebook.com/docs/plugins/like-button) to your site.

#### Example

```
[iv-facebook-like-button href='https://www.facebook.com/intervarsity.usa' layout='standard' action='like' share='true' show-faces='true' width='300']
```

### Email link shortcode

For securely displaying email addresses in page and post content, the
InterVarsity Plugin includes an `[iv-email-link]` shortcode. The shortcode works
by encoding the email address you enter so as to deter spam bots from harvesting
it. This encoding isn't guaranteed to stop *all* spam bots, but it should help
nonetheless.

#### Example

```
[iv-email-link email="me@example.com"]Email me[/iv-email-link]
```

### Cyclone Slider 2 Integration

The plugin also integrates with the [Cyclone Slider
2](https://wordpress.org/plugins/cyclone-slider-2/) plugin by allowing you to
assign sliders to particular pages on your site. A new box titled **Featured
Slider** is added to the **Edit** screen for an individual page.

The InterVarsity Theme will always show the assigned slider for any page which
has one assigned to it. However, if you design your own theme to integrate with
the InterVarsity Plugin, you should ensure that your theme respects the slider
set by a page.
