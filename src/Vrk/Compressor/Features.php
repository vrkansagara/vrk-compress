<?php

namespace Vrk\Compressor;

/**
 * Customize available features
 *
 * @since 1.3.0
 */
class Features
{
    const HTML = 'html';

    const CSS = 'css';

    const REMOVE_COMMENT = 'remove-comment';

    /**
     * Embed a single Vine
     *
     * @since 1.3.0
     *
     * @link https://dev.twitter.com/web/vine Vine Embed
     *
     * @type string
     */
    const EMBED_VINE = 'embed-vine';

    /**
     * Embed the latest Tweets from a Twitter profile by URL or shortcode
     *
     * @since 2.0.0
     *
     * @link https://dev.twitter.com/web/embedded-timelines/user Embedded Profile Timeline
     *
     * @type string
     */
    const EMBED_PROFILE = 'embed-profile';

    /**
     * Embed the latest Tweets from a list of Twitter accounts by URL or shortcode
     *
     * @since 2.0.0
     *
     * @link https://dev.twitter.com/web/embedded-timelines/list Embedded List Timeline
     *
     * @type string
     */
    const EMBED_LIST = 'embed-list';

    /**
     * Embed recent Tweets matching a search query configured at twitter.com/settings/widgets
     *
     * @since 2.0.0
     *
     * @link https://dev.twitter.com/web/embedded-timelines/search Embedded Search Timeline
     *
     * @type string
     */
    const EMBED_SEARCH = 'embed-search';

    /**
     * Embed multiple Tweets in a vertical format by URL or shortcode
     *
     * @since 2.0.0
     *
     * @link https://dev.twitter.com/web/embedded-timelines/collection Embedded Collection
     *
     * @type string
     */
    const EMBED_COLLECTION = 'embed-collection';

    /**
     * Embed multiple Tweets in a vertical format by URL or shortcode
     *
     * @since 2.0.0
     *
     * @link https://dev.twitter.com/web/embedded-timelines/collection Embedded Collection
     *
     * @type string
     */
    const EMBED_COLLECTION_GRID = 'embed-collection-grid';

    /**
     * Embed a Twitter Moment by URL or shortcode
     *
     * @since 1.3.0
     *
     * @link https://dev.twitter.com/web/embedded-moments Embedded Moment
     *
     * @type string
     */
    const EMBED_MOMENT = 'embed-moment';

    /**
     * Display a Twitter follow button using a shortcode
     *
     * @since 1.3.0
     *
     * @link https://dev.twitter.com/web/follow-button Follow Button
     *
     * @type string
     */
    const FOLLOW_BUTTON = 'follow';

    /**
     * Display a Tweet button using a shortcode
     *
     * @since 1.3.0
     *
     * @link https://dev.twitter.com/web/tweet-button Tweet Button
     *
     * @type string
     */
    const TWEET_BUTTON = 'share';

    /**
     * Display a Periscope On Air button using a shortcode
     *
     * @since 1.3.0
     *
     * @link https://www.periscope.tv/embed Periscope On Air embed
     *
     * @type string
     */
    const PERISCOPE_ON_AIR = 'periscope-on-air';

    /**
     * Audience conversion pixel
     *
     * Track your website's audience for retargeting on Twitter
     * Track the result of a Twitter targeted user visiting your webpage
     *
     * @since 1.3.0
     *
     * @link https://business.twitter.com/en/help/campaign-setup/campaign-targeting/tailored-audiences-from-web.html Twitter website tag for remarketing
     * @link https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html Twitter conversion tracking for websites
     *
     * @type string
     */
    const TRACKING_PIXEL = 'ad-pixel';

    /**
     * All available features
     *
     * Allows a publisher to filter features available on the site
     *
     * @since 1.3.0
     *
     * @type array
     */
    public static $features = array(
        self::CARDS                 => true, // Twitter Cards
        self::EMBED_TWEET           => true, // single Tweet
        self::EMBED_TWEET_VIDEO     => true, // single Tweet with video-specific display template
        self::EMBED_VINE            => true, // single Vine
        self::EMBED_PROFILE         => true, // Twitter profile
        self::EMBED_LIST            => true, // Twitter List
        self::EMBED_SEARCH          => true, // Twitter search
        self::EMBED_COLLECTION      => true, // multiple Tweets organized into a collection
        self::EMBED_COLLECTION_GRID => true, // multiple Tweets organized into a collection displayed in a grid format
        self::EMBED_MOMENT          => true, // Twitter Moment
        self::FOLLOW_BUTTON         => true, // Twitter Follow button
        self::TWEET_BUTTON          => true, // Tweet button
        self::PERISCOPE_ON_AIR      => true, // Periscope On Air button
        self::TRACKING_PIXEL        => true, // audience and conversion pixel
    );

    /**
     * Get a list of features enabled for the current site
     *
     * Allows a site owner to install the plugin but limit its features
     *
     * @since 1.3.0
     *
     * @return array list of features {
     *   List of features
     *
     *   @type string $key   feature name
     *   @type bool   $value true
     * }
     */
    public static function getEnabledFeatures()
    {
        /**
         * Limit the features available in the Twitter plugin for WordPress
         *
         * Disable specific features of the Twitter plugin for WordPress
         *
         * @since 1.3.0
         *
         * @param array $features {
         *
         *   @type string $feature feature toggle. if present, the feature is active.
         *   @type bool   $exists  used to form an associative array. a false value has no effect
         * }
         */
        $features = apply_filters( 'twitter_features', static::$features );
        if ( ! is_array( $features ) ) {
            $features = array();
        }

        return $features;
    }
}
