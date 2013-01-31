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
 * A class.
 * @package BeeHub
 *
 */
abstract class BeeHub_Principal_Collection
extends BeeHub_Directory
implements DAVACL_Principal_Collection {


public function create_member( $name ) {
  throw new DAV_Status(DAV::HTTP_FORBIDDEN);
}
public function method_DELETE( $name ) {
  throw new DAV_Status(DAV::HTTP_FORBIDDEN);
}
public function method_MOVE( $member, $destination ) {
  throw new DAV_Status(DAV::HTTP_FORBIDDEN);
}
public function method_MKCOL( $name ) {
  throw new DAV_Status(DAV::HTTP_FORBIDDEN);
}


public function user_prop_getcontenttype() {
  //return 'httpd/unix-directory';
  return BeeHub::best_xhtml_type() . '; charset="utf-8"';
}


public function user_set_getcontenttype($value) {
  throw new DAV_Status(
    DAV::HTTP_FORBIDDEN,
    DAV::COND_CANNOT_MODIFY_PROTECTED_PROPERTY
  );
}


public function report_principal_match($input) {
}


public function report_principal_search_property_set() {
  return array('DAV: displayname' => 'Name');
}

protected $members = null;
protected $current = 0;

abstract protected function init_members();

public function current() {
  if (null === $this->members)
    $this->init_members();
  return $this->members[$this->current];  
}
public function key()     {
  return $this->current;
}
public function next()    {
  $this->current++;
}
public function rewind()  {
  $this->current = 0;
}
public function valid()   {
  if (null === $this->members)
    $this->init_members();
  return $this->current < count($this->members);
}

} // class BeeHub_Principal


