MantisToTeamForgePlugin
=======================

Utilizing the Teamforge API, this is a plugin for MantisBT that will create an import on bug pages to allow you to import bugs from Mantis to TeamForge at a click of a button.


This plugin will allow you to custom configure you own projects and artifact types from the configuration menu.  It will also include a list of standard imports such as Title and Description.

This plugin has been been sandboxed within the TeamForgeConnector folder. All configuration data is stored within text files in the plugin folder. You can zip it up and transport the config and all to another machine without the need to backup data in tables and restore from backup.

TO use this plugin you will need to do the following:

1) Set up your Teamforge Import ID on pages/CreateDefect.php

$login_array['userName'] = 'TeamForgeUserId';

$login_array['possword'] = 'TeamforgePassword';

