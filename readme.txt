=== scr0bbled ===
Contributors: 01001111
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=11216463
Tags: widget, music, lastfm
Requires at least: 2.0.2
Tested up to: 2.7
Stable tag: trunk

Display the latest [available] album artwork from tracks recently scrobbled to LastFM.  LastFM API Key required.

== Description ==

The scr0bbled widget/plugin will allow you to display a variable (and approximate) list of album images from tracks recently scrobbled to LastFM for any specific user.

* Note: A LastFM API key is required for this widget to work.  See http://www.last.fm/api for more information.

== Installation ==


1. Upload 'scr0bbled.php' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the widget in the widget section.

To use as a plugin in your theme, include the following:
`<php	$s = new scr0bbled($apiKey,$user,$nAlbums,$imageSize,$title,$divid);
	echo $s->latestAlbums(); ?>`

And set the appropriate variables or leave blank for defaults ($apiKey and $user are required).

The configuration parameters are:

* Last.FM API Key: Your LastFM API key.
* Last.FM User:	 The username of the LastFM account you wish to follow (ideally your own).
* Number of Albums to Display: The number of albums the widget will attempt to display.
* Image Size: The album image size (small, medium, large) the widget will try to grab.  Image sizes may vary and sometimes a low-res version is not available so be sure to do some image scaling in your CSS styling.
* Title: The title of this section.
* Wrapper Div ID: The id for the widget's wrapper div for your CSS styling convenience.  Leave blank to omit the div wrapper entirely.


== Frequently Asked Questions ==

= Why do I need a LastFM API key? =

Because it is required to use the LastFM API and you can't have mine.

= I just listened to / scrobbled a song and the image isn't showing up.  Why? =

If there is no album art associated with that track on LastFM then it will not be returned to the widget.  Submit the appropriate artwork to LastFM to make it available to the widget and the world over.

= Less than X albums are showing up when I've clearly selected X =

There's no way to grab a certain number of recently listened albums in one request so, rather than assaulting LastFM with multiple requests, I've used 10 as a multiplier to approximate the tracks needed to get the requested number of albums.  Tracks without artwork or albums with a large number of tracks (like Pg Destroyer's "37 Counts of Battery") can thwart this valiant effort.

= Why do some albums show up multiple times? =

If the album name associated with one track differs slightly from that of another track that is supposed to be from the same album (such as happens with His Hero Is Gone - "Fifteen Counts of Arson" which is also listed at times as "15 Counts of Arson") then, unfortunately, the album will appear twice.  Again, try rectify this problem through LastFM by making sure all of the album associations are correct and consistent.

== Screenshots ==

None at the moment.


== Changelog ==

= 1.0.3 =
* Miscellaneous fixes.

= 1.0.2 =
* Fixed bug where title may not have been set.

= 1.0.1 =
* Implemented non PHP 5 query string builder.

= 1.0.0 =
* First release.

== Upgrade Notice ==

= 1.0.3 =
* Miscellaneous fixes.

= 1.0.2 =
* Fixed bug where title may not have been set.

= 1.0.1 =
* Implemented non PHP 5 query string builder.
