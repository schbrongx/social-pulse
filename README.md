=== Social Pulse ===
Contributors: schbrongx
Tags: social, followers, api, counters
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.1.2
License: MIT
License URI: https://mit-license.org/

# Social Pulse
... is a WP plugin showing follower counts from YouTube, Facebook, X, and Steam. It uses Leader/Follower modes to reduce API requests.

## General Idea
Social Pulse retrieves follower numbers from multiple platforms and displays them via WordPress shortcodes. In Leader mode, the plugin directly makes API requests to obtain the latest values, which are then cached and exposed at a dedicated URL. In Follower mode, the plugin skips direct API calls and instead retrieves the pre-fetched data from the Leader instance.
* [Screenshot Leader-Mode](assets/screenshot-leader-mode.png)
* [Screenshot Follower-Mode](assets/screenshot-follower-mode.png)

## Try it out!
* Download the [ZIP](https://github.com/schbrongx/social-pulse/releases/download/v1.0/social-pulse.zip)
* Navigate to the [Wordpress Playground](https://playground.wordpress.net/)
* Go to the "Plugins" page
* Click "Add New Plugin"
* Click "Upload Plugin"
* Select the ZIP and click "Install Now"
* Click "Activate Plugin"
The plugin should now be visible at the bottom of the Wordpress menu.

## Leader and Follower Mode
* Leader Mode:
In this mode, Social Pulse performs the actual API calls to get the latest follower counts and caches these values. The current values are also exposed at a special URL (e.g. /social-counters/values), which can be used by Follower instances.

* Follower Mode:
Follower mode is designed to minimize API calls on installations that do not need to fetch data directly. Instead of making API requests, the plugin retrieves the cached values from the Leader URL. This is particularly useful when dealing with strict API rate limits.

## How to Use Social Pulse in Leader Mode
1. Activate Leader Mode:
In the plugin settings, select Leader as the mode.
2. Configure API Credentials:
Enter your API keys, tokens, and IDs for YouTube, Facebook, X, and Steam in the respective fields.
3. Save Changes:
Make sure to save your settings.
4. Retrieve Values:
The plugin will automatically fetch and cache the current follower counts. The exposed values URL (displayed on the settings page) can be used by Follower installations.

## How to Use Social Pulse in Follower Mode
1. Activate Follower Mode:
In the plugin settings, select Follower as the mode.
2. Enter Leader URL:
Provide the URL from a Leader installation (the URL where the leader exposes its cached values).
3. Save Changes:
Save your settings to store the Leader URL.
4. Automatic Data Fetch:
The plugin will now retrieve the follower values from the Leader URL every 5 minutes or whenever manually tested. Direct API requests are disabled in this mode.

## Where to Find the Necessary Information
For each platform, you will need to obtain API keys, tokens, or IDs. Here are some helpful links to get you started:

* YouTube:
[YouTube Data API v3 - Getting Started](https://developers.google.com/youtube/v3/getting-started)
(Find your API key and channel ID here.)

* Facebook:
Facebook for Developers, [Long Lasting Tokens for Clients](https://developers.facebook.com/docs/facebook-login/guides/access-tokens/get-long-lived/#long-via-code)
(Learn how to create an app, get an access token, and locate your Page ID.)

* X (formerly Twitter):
X Developer Platform, [Generating and Using an App-Only Bearer-Token](https://docs.x.com/resources/fundamentals/authentication/oauth-2-0/bearer-tokens)
(Obtain a Bearer Token and set up your developer credentials.)

* Steam:
[Steam Web API Documentation](https://steamcommunity.com/dev)
(Find information on obtaining your Steam App ID and using the API.)
