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
 * @package BeeHub
 */

/**
 * Some class.
 * @package BeeHub
 */
class BeeHub_Users extends BeeHub_Principal_Collection {

  /**
   * Returns either a form to register a new user or a form to verify your e-mail address. No authentication required.
   * @see DAV_Resource::method_GET
   */
  public function method_GET() {
    if ( empty($_SERVER['HTTPS']) &&
         APPLICATION_ENV != BeeHub::ENVIRONMENT_DEVELOPMENT ) {
      throw new DAV_Status(
        DAV::HTTP_MOVED_PERMANENTLY,
        BeeHub::urlbase(true) . $_SERVER['REQUEST_URI']
      );
    }
    $as = BeeHub_Auth::inst()->simpleSaml();
    $display_name = '';
    $email_address = '';
    if (!is_null($as)) {
      $attrs = $as->getAttributes();
      $display_name = $attrs['urn:mace:dir:attribute-def:displayName'][0];
      $email_address = $attrs['urn:mace:dir:attribute-def:mail'][0];
    }
    $this->include_view('new_user', array('display_name'=>$display_name, 'email_address'=>$email_address));
  }


  /**
   * Handles both the form to register a new user and the form to verify an e-mail address. No authentication required.
   * @see DAV_Resource::method_POST()
   */
  public function method_POST(&$headers) {
    // TODO: translate user_name to ASCII and check for double usernames
    $user_name = $_POST['user_name'];
    $displayname = $_POST['displayname'];
    $email = $_POST['email'];
    $password = (!empty($_POST['password']) ? $_POST['password'] : null);

    // Store in the database
    $statement = BeeHub_DB::execute(
      'INSERT INTO `beehub_users`
          (`user_name`)
        VALUES (?)',
      's', $user_name
    );

    // Fetch the user and store extra properties
    $user = BeeHub_Registry::inst()->resource(
      BeeHub::$CONFIG['namespace']['users_path'] . rawurlencode($user_name)
    );
    $user->user_set(BeeHub::PROP_PASSWORD, $password);
    $user->user_set(DAV::PROP_DISPLAYNAME, $displayname);
    $user->user_set(BeeHub::PROP_EMAIL, $email);
    $auth = BeeHub_Auth::inst();
    if ($auth->surfconext()) {
      $surfId = $auth->simpleSaml()->getAuthData("saml:sp:NameID");
      $surfId = $surfId['Value'];
      $user->user_set(BeeHub::PROP_SURFCONEXT, $surfId);
    }
    $user->storeProperties();

    $this->include_view('new_user_confirmation', array('email_address'=>$email_address));
  }

  public function report_principal_property_search($properties) {
    if ( 1 != count( $properties ) ||
         ! isset( $properties[DAV::PROP_DISPLAYNAME] ) ||
         1 != count( $properties[DAV::PROP_DISPLAYNAME] ) )
      throw new DAV_Status(
        DAV::HTTP_BAD_REQUEST,
        'You\'re searching for a property which cannot be searched.'
      );
    $match = $properties[DAV::PROP_DISPLAYNAME][0];
    $match = str_replace(array('_', '%'), array('\\_', '\\%'), $match) . '%';
    $stmt = BeeHub_DB::execute(
      'SELECT `user_name`
       FROM `beehub_users`
       WHERE `displayname` LIKE ?', 's', $match
    );
    $retval = array();
    while ($row = $stmt->fetch_row()) {
      $retval[] = BeeHub::$CONFIG['namespace']['users_path'] .
        rawurlencode($row[0]);
    }
    $stmt->free_result();
    return $retval;
  }


  protected function init_members() {
    $stmt = BeeHub_DB::execute('SELECT `user_name` FROM `beehub_users`');
    $this->members = array();
    while ($row = $stmt->fetch_row()) {
      $this->members[] = rawurlencode($row[0]);
    }
    $stmt->free_result();
  }


  /**
  * @see BeeHub_Resource::user_prop_acl_internal()
  */
  public function user_prop_acl_internal() {
    return array( new DAVACL_Element_ace(
      DAVACL::PRINCIPAL_ALL, false, array(
        DAVACL::PRIV_READ, DAVACL::PRIV_READ_ACL
      ), false, true
    ));
  }


} // class BeeHub_Users
