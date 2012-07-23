karotz-wordpressplugin
======================

Wordpress-Plugin for Karotz.

Provides a Widget, that enables the users of your Blog to notify you with a Karotz-Rabbit.
If a user writes a comment, you get a notification as well.

Status
======

Under development!

Issues
======
- Statistics don't work yet.
- Neccessary Karotz-Descriptor is not yet published.
- Documentation not yet available

Installation
============

Part I: Karotz
--------------
Install the Karotz-App on your rabbit. (http://www.karotz.com/appz/app/test?apikey=c2f05ab2-d347-4551-94e7-28bbe1fb52d1&version=0.1&sign=812875d0aa2a93583fd411681ba2a073)
Remember the Install-Id for later configuration.

Part II: Wordpress
------------------
Download the Zip-File (https://github.com/michaelkrueger/karotz-wordpressplugin/zipball/master) 
Upload it to your WordPress-Site: Dashboard - Plugins: Upload - Choose the Zip-File
Activate the plugin.
Activate curl-extension (php.ini): Uncomment "extension=php_curl.dll"

Configure the Karotz-Widget: Dashboard - Appearance - Widget
Drag the Karotz-Widget somewhere. Press the down-button and enter an appropriate name, a kind text for motivation and the above mentioned Install-Id.

Part III: Test
--------------
Test it and have fun!
