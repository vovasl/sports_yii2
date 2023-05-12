<?php
/** https://pinnacleapi.github.io/ */

namespace backend\components\pinnacle\services;


/**
 * PHP Pinnacle Sports API Client
 */
class Client
{
  /**
   * Pinnacle Sports API Base url
   */
  const BASE_URL = "https://api.pinnaclesports.com/";

  private $version = "v1";

  /**
   * @var string or null
   */
  private $credentials;

  /**
   * Constructor
   * @param string $userid
   * @param null $pass
   */
  public function __construct($userid, $pass = null) {
    // Credentials: <Base64 value of UTF-8 encoded “username:password”>
    $this->credentials = base64_encode($userid . ":" . $pass);
  }

  /**
   * Determine JSON format
   * @param string $string
   */
  private function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }

  /**
   * Convert XML format to JSON format
   * @param string $arg
   */
  private function returnJsonFormat($arg) {
    // If the argument is not JSON, converts it to JSON
    if ($this->isJson($arg)) {
      // JSON
      $ret = $arg;
    } else {
      // XML -> JSON
      $xmlDocument = simplexml_load_string($arg);
      $ret = json_encode($xmlDocument);
    }
    return $ret;
  }

  /**
   * Generate HTTP header
   */
  private function getHTTPHeader() {
    // Build the header
    $header[] = "Content-type: application/json";
    $header[] = "Authorization: Basic " . $this->credentials;
    return $header;
  }

  /**
   * Create API query and execute a GET/POST request
   * @param string $httpMethod GET/POST
   * @param string $endpoint
   * @param string $options
   */
  private function apiCall($httpMethod, $endpoint, $options) {
    // Create URL
    $api_url = self::BASE_URL . $this->version . "/" . $endpoint;
    // POST method or GET method
    if(strtolower($httpMethod) === "post") {
      $jsonOptions = $this->returnJsonFormat($options);
    } else {
      if(count($options) > 0) {
        $api_url .= "?" . http_build_query($options);
      }
    }

    // Set up a CURL channel.
    $httpChannel = curl_init();

    // Prime the channel
    curl_setopt($httpChannel, CURLOPT_URL, $api_url);
    curl_setopt($httpChannel, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($httpChannel, CURLOPT_HTTPHEADER, $this->getHTTPHeader());
    // Unless you have all the CA certificates installed in your trusted root authority, this should be left as false.
    curl_setopt($httpChannel, CURLOPT_SSL_VERIFYPEER, false);
    // POST method
    if(strtolower($httpMethod) === "post") {
      curl_setopt($httpChannel, CURLOPT_POST, true);
      curl_setopt($httpChannel, CURLOPT_POSTFIELDS, $jsonOptions);
    }
    // This fetches the initial feed result. Next we will fetch the update using the fdTime value and the last URL parameter
    $response = curl_exec($httpChannel);
    curl_close($httpChannel);

    // Return JSON or XML
    return $response;
  }

  /**
   * Call GET request
   * @param string $endpoint
   * @param string $options
   */
  private function get($endpoint, $options = null) {
    return $this->apiCall("get", $endpoint, $options);
  }

  /**
   * Call POST request
   * @param string $endpoint
   * @param string $options
   */
  private function post($endpoint, $options = null) {
    return $this->apiCall("post", $endpoint, $options);
  }

  /**
   * Get Sports
   */
  public function getSports()
  {
    return $this->get("sports");
  }

  /**
   * Get Leagues
   * @param array $options
   * @return bool|string
   */
  public function getLeagues(array $options)
  {
    $this->version = "v2";
    return $this->get("leagues", $options);
  }

  /**
   * Get Feed
   * @param string $options
   */
  public function getFeed($options)
  {
    return $this->get("feed", $options);
  }

  /**
   * @param $options
   * @return bool|string
   */
  public function getPeriods($options)
  {
    return $this->get("periods", $options);
  }

  /**
   * Get Fixtures
   * @param array $options
   * @return bool|string
   */
  public function getFixtures(array $options)
  {
    return $this->get("fixtures", $options);
  }

  /**
   * @param $options
   * @return bool|string
   */
  public function getFixturesSpecial($options)
  {
    return $this->get("fixtures/special", $options);
  }

  /**
   * Get Odds
   * @param array $options
   * @return bool|string
   */
  public function getOdds(array $options)
  {
    return $this->get("odds", $options);
  }

  /**
   * Get Parlay Odds
   * @param string $options
   */
  public function getParlayOdds($options)
  {
    return $this->get("odds/parlay", $options);
  }

  /**
   * Get Currencies
   */
  public function getCurrencies()
  {
    $this->version = "v2";
    return $this->get("currencies");
  }

  /**
   * Get Client Balance
   */
  public function getClientBalance()
  {
    return $this->get("client/balance");
  }

  /**
   * Place Bet
   * @param string $options
   */
  public function placeBet($options)
  {
    return $this->post("bets/place", $options);
  }

  /**
   * Place Parlay Bet
   * @param string $options
   */
  public function placeParlayBet($options)
  {
    return $this->post("bets/parlay", $options);
  }

  /**
   * Get Line
   * @param string $options
   */
  public function getLine($options)
  {
    return $this->get("line", $options);
  }

  /**
   * Get Parlay Line
   * @param string $options
   */
  public function getParlayLine($options)
  {
    return $this->post("line/parlay", $options);
  }

  /**
   * Get Bets
   * @param string $options
   */
  public function getBets($options)
  {
    return $this->get("bets", $options);
  }

  /**
   * Get Inrunning
   */
  public function getInrunning()
  {
    return $this->get("inrunning");
  }

  /**
   * Get Translations
   * @param string $options
   */
  public function getTranslations($options)
  {
    return $this->get("translations", $options);
  }
}
?>
