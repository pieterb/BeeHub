<?php

/*·************************************************************************
 * Copyright ©2007-2012 SARA b.v., Amsterdam, The Netherlands
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at <http://www.apache.org/licenses/LICENSE-2.0>
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **************************************************************************/

/**
 * File documentation (who cares)
 * @TODO For each occurrence of DAV::HTTP_FORBIDDEN in all BeeHub code, check
 *   if it should be replaced with a call to DAV::forbidden(). Originally, we
 *   expected that BeeHub would only have authenticated users, but this is no
 *   longer the case, so we must start to distinguish between FORBIDDEN and
 *   UNAUTHORIZED.
 * @package BeeHub
 */

require_once dirname(dirname(__FILE__)) . '/webdav-php/lib/dav.php';

/**
 * A MySQL exception
 * @package BeeHub
 */
class BeeHub_MySQL extends Exception {

}

/**
 * A deadlock occured: Try again.
 * @package BeeHub
 */
class BeeHub_Deadlock extends BeeHub_MySQL {

}

/**
 * Out of resources: maybe later.
 * @package BeeHub
 */
class BeeHub_Timeout extends BeeHub_MySQL {

}

/**
 * Just a namespace.
 * @package BeeHub
 */
class BeeHub {

  const PROP_NAME          = 'http://beehub.nl/ name';
  const PROP_PASSWD        = 'http://beehub.nl/ password';
  const PROP_EMAIL         = 'http://beehub.nl/ email';
  const PROP_X509          = 'http://beehub.nl/ x509';
  const PROP_DESCRIPTION   = 'http://beehub.nl/ description';
  const PROP_GROUP_ADMIN_SET            = 'http://beehub.nl/ group-admin-set';
  const PROP_GROUP_REQUESTED_MEMBER_SET = 'http://beehub.nl/ group-requested-member-set';

  /**#@+
   * These constants define the different environments the code can run in.
   *
   * The global constant APPLICATION_ENV can be compared to one of these
   * constants to check whether the application is running in the respective
   * environment. This reduces the chance of developers making up their own
   * environment values without in stead of using one of the existing ones.
   */
  const ENVIRONMENT_DEVELOPMENT = 'development';
  const ENVIRONMENT_PRODUCTION  = 'production';
  /**#@-*/

  public static $CONFIG;

  /**
   * A better escapeshellarg.
   * The default PHP version seems not to work for UTF-8 strings...
   * @return string
   * @param string $arg
   */
  public static function escapeshellarg($arg) {
    return "'" . str_replace("'", "'\\''", $arg) . "'";
  }

  public static function localPath($path) {
    return DAV::unslashify(self::$CONFIG['environment']['datadir'] . rawurldecode($path));
  }

  /**
   * @var mysqli
   */
  private static $MYSQLI = null;

  /**
   * @return mysqli
   * @throws DAV_Status
   */
  public static function mysqli() {
    if (self::$MYSQLI === null) {
      self::$MYSQLI = new mysqli(
                      BeeHub::$CONFIG['mysql']['host'],
                      BeeHub::$CONFIG['mysql']['username'],
                      BeeHub::$CONFIG['mysql']['password'],
                      BeeHub::$CONFIG['mysql']['database']
      );
      if (!self::$MYSQLI)
        throw new BeeHub_MySQL(mysqli_connect_error(), mysqli_connect_errno());
    }
    return self::$MYSQLI;
  }

  public static function escape_string($string) {
    return is_null($string) ? 'NULL' : '\'' . self::mysqli()->escape_string($string) . '\'';
  }

  public static function ETag($etag = null) {
    if (is_null($etag)) {
      self::query('INSERT INTO ETag VALUES();');
      $etag = self::mysqli()->insert_id;
      if (!($etag % 100))
        self::query("DELETE FROM ETag WHERE etag < $etag");
    }
    return '"' . trim(base64_encode(pack('H*', dechex($etag))), '=') . '"';
  }

  /**
   * @param string $query
   * @return void
   * @throws BeeHub_Deadlock|BeeHub_Timeout|BeeHub_MySQL
   */
  public static function real_query($query) {
    if (!self::mysqli()->real_query($query)) {
      if (self::mysqli()->errno == 1213)
        throw new BeeHub_Deadlock(self::mysqli()->error);
      if (self::mysqli()->errno == 1205)
        throw new BeeHub_Timeout(self::mysqli()->error);
      throw new BeeHub_MySQL(self::mysqli()->error, self::mysqli()->errno);
    }
  }

  /**
   * @param string $query
   * @return mysqli_result
   * @throws Exception
   */
  public static function query($query) {
    if (!( $retval = self::mysqli()->query($query) )) {
      if (self::mysqli()->errno == 1213)
        throw new BeeHub_Deadlock(self::mysqli()->error);
      if (self::mysqli()->errno == 1205)
        throw new BeeHub_Timeout(self::mysqli()->error);
      throw new BeeHub_MySQL(self::mysqli()->error, self::mysqli()->errno);
    }
    return $retval;
  }

  /**
   * @return string a uuid, generated by MySQL
   */
  public static function uuid() {
    $result = self::query('SELECT UUID();');
    $row = $result->fetch_row();
    return $row[0];
  }

  public static function best_xhtml_type() {
    return 'text/html';
    // The rest of the function will be skipped. This is because ExtJS doesn't support X(HT)ML, so we always need to send it as 'text/html'
    return ( false === strstr(@$_SERVER['HTTP_USER_AGENT'], 'MSIE') &&
            false === strstr(@$_SERVER['HTTP_USER_AGENT'], 'Microsoft') ) ?
            'application/xhtml+xml' : 'text/html';
  }

  /**
   * @todo implement
   * @todo deprecate?
   */
  public static function current_user() {
    return BeeHub_ACL_Provider::inst()->CURRENT_USER_PRINCIPAL;
  }

  public static function handle_method_spoofing() {
    $_SERVER['ORIGINAL_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST' and
            isset($_GET['_method'])) {
      $http_method = strtoupper($_GET['_method']);
      unset($_GET['_method']);
      if ($http_method === 'GET' &&
              strstr(@$_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {
        $_GET = $_POST;
        $_POST = array();
      }
      $_SERVER['QUERY_STRING'] = http_build_query($_GET);
      $_SERVER['REQUEST_URI'] =
              substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
      if ($_SERVER['QUERY_STRING'] != '')
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
      $_SERVER['REQUEST_METHOD'] = $http_method;
    }
  }

} // class BeeHub

BeeHub::$CONFIG = parse_ini_file(
        dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config.ini', true
);

