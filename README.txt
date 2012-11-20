=== LastFM Top Artists ===
Contributors: alairock
Tags: lastfm, lastfm top tracks, lastfm top artists, top, artists, songs, music,
Requires at least: 3.0.0
Tested up to: 3.4.1
Stable tag: 0.5.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Displays the top LastFM artists for a particular user.

== Description ==

This plugin creates a widget for displaying your LastFM Artists, and the number of plays per artist. Awesome right?

Options available: LastFM Username, Number of results


== Installation ==

It's easiest to install through the Plugin section of your dashboard. Just search "LastFM Top Artists" by alairock.


If you must do it manually:
1. Download and extract the plugin, then upload it to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Do I need a LastFM account? =

Yes, you will need the username to link your top played artists.

= How do I get it to start working? =

It's automatic. You need a LastFM account, and you need to be using it. That's it. If you have questions using LastFM, consult their support pages.

== Screenshots ==

1. Shot of the plugin widget in action!
2. Admin settings

== Changelog ==

= COMING SOON =
* Skins. The lists are not stylized at all in it's current form. This is coming really soon.
* If you have suggestions, please contact me, or submit the idea in the WordPress forum: http://wordpress.org/support/plugin/lastfm-top-artists

= 0.5.0 =
* Created a static file that gets updated on a setting change or every 3 hours. This has taken the load time for the plugin from 1.2 seconds average load time to .0002 after initial page load or cache regeneration. (On my local machine of course)

= 0.4.0 =
* Updated to use the actual API, instead of just calling their RSS feeds.
* Cleaned up the logic for displaying the different types of lists.

= 0.3.0 =
* Updated version number

= 0.2.0 =
* Updated version to proper format x.x.x
* Now you can select between Top Artists, Top Albums, Top Tracks
* You can also select time frame. Last 12 months, Last 6 Months, Last 3 months, Last 7 days, Alltime. (1 month not possible due to lFM limitation)

= 0.1 =
* Initial Commit
