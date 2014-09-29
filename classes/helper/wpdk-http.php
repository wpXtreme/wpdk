<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Standard HTTP verbs
 *
 * @class           WPDKHTTPVerbs
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-10-31
 * @version         1.0.0
 * @since           1.3.1
 *
 */
class WPDKHTTPVerbs {

  const DELETE = 'DELETE';
  const GET    = 'GET';
  const POST   = 'POST';
  const PUT    = 'PUT';
  const PATCH  = "PATCH";

  /**
   * Return the key-value array filtered request methods
   *
   * @brief Request methods
   */
  public function requestMethods()
  {
    /* Standard default verbs. */
    $verbs = array(
      self::POST   => self::POST,
      self::GET    => self::GET,
      self::DELETE => self::DELETE,
      self::PUT    => self::PUT,
    );

    return apply_filters( 'wpdk_http_verbs', $verbs );
  }
}

/**
 * HTTP Request helper class
 *
 * @class           WPDKHTTPRequest
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-10-31
 * @version         1.0.0
 * @since           1.3.1
 *
 */
class WPDKHTTPRequest {

  /**
   * Return TRUE if we are called by Ajax. Used to be sure that we are responding to an HTTPRequest request and that
   * the WordPress define DOING_AJAX is defined.
   *
   * @brief Ajax validation
   *
   * @return bool TRUE if Ajax trusted
   */
  public static function isAjax()
  {
    if ( defined( 'DOING_AJAX' ) ) {
      return true;
    }
    if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
      strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'
    ) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Return true if the $verb param in input match with REQUEST METHOD
   *
   * @brief Check request
   *
   * @param string $verb The verb, for instance; GET, WPDKHTTPVerbs::GET, delete, etc...
   *
   * @return bool
   */
  public static function isRequest( $verb )
  {
    $verb = strtolower( $verb );
    return ( $verb == strtolower( $_SERVER['REQUEST_METHOD'] ) );
  }

  /**
   * Return true if the REQUEST METHOD is GET
   *
   * @brief Check if request is get
   *
   * @return bool
   */
  public static function isRequestGET()
  {
    return self::isRequest( WPDKHTTPVerbs::GET );
  }

  /**
   * Return true if the REQUEST METHOD is POST
   *
   * @brief Check if request is POST
   *
   * @return bool
   */
  public static function isRequestPOST()
  {
    return self::isRequest( WPDKHTTPVerbs::POST );
  }


}

/**
 * User Agent class
 *
 * @class           WPDKUserAgents
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-25
 * @version         1.0.0
 * @since           1.6.0
 *
 */
class WPDKUserAgents {

  /**
   * Return a key value pairs array with the complete list of crawler user agents.
   *
   * @return array
   */
  public static function crawlers()
  {
    $user_agents = self::userAgents();

    return $user_agents['crawler'];
  }

  /**
   * Return TRUE if an user agent or current user agent is a Crawler.
   *
   * @param string $user_agent Optional. User agent string oro empty for current user agent.
   *
   * @return bool
   */
  public static function isCrawler( $user_agent = '' )
  {
    // Get and sanitize user agent
    $user_agent = trim( empty( $user_agent ) ? $_SERVER['HTTP_USER_AGENT'] : $user_agent, ' \t\r\n\0\x0B' );

    // Get crawler (key)
    $crawlers = implode( '|', array_keys( self::crawlers() ) );

    // Pos
    $pos = stripos( $crawlers, $user_agent );

    return ( false !== $pos);

  }

  /**
   * Return a key value pairs array with the complete list of user agents for type.
   *
   * @brief User agents
   * @todo  complete with list of browser, etc...
   *
   * @return array
   */
  public static function userAgents()
  {
    $user_agent = array(

      'crawler' => array(
        '008'                      => '008',
        'ABACHOBot'                => 'ABACHOBot',
        'Accoona-AI-Agent'         => 'Accoona-AI-Agent',
        'AddSugarSpiderBot'        => 'AddSugarSpiderBot',
        'alexa'                    => 'alexa',
        'AnyApexBot'               => 'AnyApexBot',
        'Arachmo'                  => 'Arachmo',
        'B-l-i-t-z-B-O-T'          => 'B-l-i-t-z-B-O-T',
        'Baiduspider'              => 'Baiduspider',
        'BecomeBot'                => 'BecomeBot',
        'BeslistBot'               => 'BeslistBot',
        'BillyBobBot'              => 'BillyBobBot',
        'Bimbot'                   => 'Bimbot',
        'Bingbot'                  => 'Bingbot',
        'BlitzBOT'                 => 'BlitzBOT',
        'boitho.com-dc'            => 'boitho.com-dc',
        'boitho.com-robot'         => 'boitho.com-robot',
        'btbot'                    => 'btbot',
        'CatchBot'                 => 'CatchBot',
        'Cerberian Drtrs'          => 'Cerberian Drtrs',
        'Charlotte'                => 'Charlotte',
        'ConveraCrawler'           => 'ConveraCrawler',
        'cosmos'                   => 'cosmos',
        'Covario IDS'              => 'Covario IDS',
        'DataparkSearch'           => 'DataparkSearch',
        'DiamondBot'               => 'DiamondBot',
        'Discobot'                 => 'Discobot',
        'Dotbot'                   => 'Dotbot',
        'EmeraldShield.com WebBot' => 'EmeraldShield.com WebBot',
        'envolk[ITS]spider'        => 'envolk[ITS]spider',
        'EsperanzaBot'             => 'EsperanzaBot',
        'Exabot'                   => 'Exabot',
        'FAST Enterprise Crawler'  => 'FAST Enterprise Crawler',
        'FAST-WebCrawler'          => 'FAST-WebCrawler',
        'FDSE robot'               => 'FDSE robot',
        'FindLinks'                => 'FindLinks',
        'FurlBot'                  => 'FurlBot',
        'FyberSpider'              => 'FyberSpider',
        'g2crawler'                => 'g2crawler',
        'Gaisbot'                  => 'Gaisbot',
        'GalaxyBot'                => 'GalaxyBot',
        'genieBot'                 => 'genieBot',
        'Gigabot'                  => 'Gigabot',
        'Girafabot'                => 'Girafabot',
        'Googlebot'                => 'Googlebot',
        'Googlebot-Image'          => 'Googlebot-Image',
        'GurujiBot'                => 'GurujiBot',
        'HappyFunBot'              => 'HappyFunBot',
        'hl_ftien_spider'          => 'hl_ftien_spider',
        'Holmes'                   => 'Holmes',
        'htdig'                    => 'htdig',
        'iaskspider'               => 'iaskspider',
        'ia_archiver'              => 'ia_archiver',
        'iCCrawler'                => 'iCCrawler',
        'ichiro'                   => 'ichiro',
        'inktomi'                  => 'inktomi',
        'igdeSpyder'               => 'igdeSpyder',
        'IRLbot'                   => 'IRLbot',
        'IssueCrawler'             => 'IssueCrawler',
        'Jaxified Bot'             => 'Jaxified Bot',
        'Jyxobot'                  => 'Jyxobot',
        'KoepaBot'                 => 'KoepaBot',
        'L.webis'                  => 'L.webis',
        'LapozzBot'                => 'LapozzBot',
        'Larbin'                   => 'Larbin',
        'LDSpider'                 => 'LDSpider',
        'LexxeBot'                 => 'LexxeBot',
        'Linguee Bot'              => 'Linguee Bot',
        'LinkWalker'               => 'LinkWalker',
        'lmspider'                 => 'lmspider',
        'lwp-trivial'              => 'lwp-trivial',
        'mabontland'               => 'mabontland',
        'magpie-crawler'           => 'magpie-crawler',
        'Mediapartners-Google'     => 'Mediapartners-Google',
        'MJ12bot'                  => 'MJ12bot',
        'Mnogosearch'              => 'Mnogosearch',
        'mogimogi'                 => 'mogimogi',
        'MojeekBot'                => 'MojeekBot',
        'Moreoverbot'              => 'Moreoverbot',
        'Morning Paper'            => 'Morning Paper',
        'msnbot'                   => 'msnbot',
        'MSRBot'                   => 'MSRBot',
        'MVAClient'                => 'MVAClient',
        'mxbot'                    => 'mxbot',
        'NetResearchServer'        => 'NetResearchServer',
        'NetSeer Crawler'          => 'NetSeer Crawler',
        'NewsGator'                => 'NewsGator',
        'NG-Search'                => 'NG-Search',
        'nicebot'                  => 'nicebot',
        'noxtrumbot'               => 'noxtrumbot',
        'Nusearch Spider'          => 'Nusearch Spider',
        'NutchCVS'                 => 'NutchCVS',
        'Nymesis'                  => 'Nymesis',
        'obot'                     => 'obot',
        'oegp'                     => 'oegp',
        'omgilibot'                => 'omgilibot',
        'OmniExplorer_Bot'         => 'OmniExplorer_Bot',
        'OOZBOT'                   => 'OOZBOT',
        'Orbiter'                  => 'Orbiter',
        'PageBitesHyperBot'        => 'PageBitesHyperBot',
        'Peew'                     => 'Peew',
        'polybot'                  => 'polybot',
        'Pompos'                   => 'Pompos',
        'PostPost'                 => 'PostPost',
        'Psbot'                    => 'Psbot',
        'PycURL'                   => 'PycURL',
        'Qseero'                   => 'Qseero',
        'Radian6'                  => 'Radian6',
        'RAMPyBot'                 => 'RAMPyBot',
        'RufusBot'                 => 'RufusBot',
        'SandCrawler'              => 'SandCrawler',
        'SBIder'                   => 'SBIder',
        'ScoutJet'                 => 'ScoutJet',
        'Scrubby'                  => 'Scrubby',
        'SearchSight'              => 'SearchSight',
        'Seekbot'                  => 'Seekbot',
        'semanticdiscovery'        => 'semanticdiscovery',
        'Sensis Web Crawler'       => 'Sensis Web Crawler',
        'SEOChat::Bot'             => 'SEOChat::Bot',
        'SeznamBot'                => 'SeznamBot',
        'Shim-Crawler'             => 'Shim-Crawler',
        'ShopWiki'                 => 'ShopWiki',
        'Shoula robot'             => 'Shoula robot',
        'silk'                     => 'silk',
        'Sitebot'                  => 'Sitebot',
        'Snappy'                   => 'Snappy',
        'sogou spider'             => 'sogou spider',
        'Sosospider'               => 'Sosospider',
        'Speedy Spider'            => 'Speedy Spider',
        'Sqworm'                   => 'Sqworm',
        'StackRambler'             => 'StackRambler',
        'suggybot'                 => 'suggybot',
        'SurveyBot'                => 'SurveyBot',
        'SynooBot'                 => 'SynooBot',
        'Teoma'                    => 'Teoma',
        'TerrawizBot'              => 'TerrawizBot',
        'TheSuBot'                 => 'TheSuBot',
        'Thumbnail.CZ robot'       => 'Thumbnail.CZ robot',
        'TinEye'                   => 'TinEye',
        'truwoGPS'                 => 'truwoGPS',
        'TurnitinBot'              => 'TurnitinBot',
        'TweetedTimes Bot'         => 'TweetedTimes Bot',
        'TwengaBot'                => 'TwengaBot',
        'updated'                  => 'updated',
        'Urlfilebot'               => 'Urlfilebot',
        'Vagabondo'                => 'Vagabondo',
        'VoilaBot'                 => 'VoilaBot',
        'Vortex'                   => 'Vortex',
        'voyager'                  => 'voyager',
        'VYU2'                     => 'VYU2',
        'webcollage'               => 'webcollage',
        'Websquash.com'            => 'Websquash.com',
        'wf84'                     => 'wf84',
        'WoFindeIch Robot'         => 'WoFindeIch Robot',
        'WomlpeFactory'            => 'WomlpeFactory',
        'Xaldon_WebSpider'         => 'Xaldon_WebSpider',
        'yacy'                     => 'yacy',
        'Yahoo! Slurp'             => 'Yahoo! Slurp',
        'Yahoo! Slurp China'       => 'Yahoo! Slurp China',
        'YahooSeeker'              => 'YahooSeeker',
        'YahooSeeker-Testing'      => 'YahooSeeker-Testing',
        'YandexBot'                => 'YandexBot',
        'YandexImages'             => 'YandexImages',
        'Yasaklibot'               => 'Yasaklibot',
        'Yeti'                     => 'Yeti',
        'YodaoBot'                 => 'YodaoBot',
        'yoogliFetchAgent'         => 'yoogliFetchAgent',
        'YoudaoBot'                => 'YoudaoBot',
        'Zao'                      => 'Zao',
        'Zealbot'                  => 'Zealbot',
        'zspider'                  => 'zspider',
        'ZyBorg'                   => 'ZyBorg',
        'crawler'                  => 'crawler',
        'bot'                      => 'bot',
        'froogle'                  => 'froogle',
        'looksmart'                => 'looksmart',
        'URL_Spider_SQL'           => 'URL_Spider_SQL',
        'Firefly'                  => 'Firefly',
        'NationalDirectory'        => 'NationalDirectory',
        'Ask Jeeves'               => 'Ask Jeeves',
        'TECNOSEEK'                => 'TECNOSEEK',
        'InfoSeek'                 => 'InfoSeek',
        'WebFindBot'               => 'WebFindBot',
        'Googlebot'                => 'Googlebot',
        'Scooter'                  => 'Scooter',
        'appie'                    => 'appie',
        'WebBug'                   => 'WebBug',
        'Spade'                    => 'Spade',
        'rabaz'                    => 'rabaz',
        'Feedfetcher-Google'       => 'Feedfetcher-Google',
        'TechnoratiSnoop'          => 'TechnoratiSnoop',
        'Rankivabot'               => 'Rankivabot',
        'Mediapartners-Google'     => 'Mediapartners-Google',
        'Sogou web spider'         => 'Sogou web spider',
        'WebAlta Crawler'          => 'WebAlta Crawler'
      )
    );

    return $user_agent;
  }
}


/// @endcond