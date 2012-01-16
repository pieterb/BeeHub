<?php

/*·************************************************************************
 * Copyright ©2007-2011 Pieter van Beek <http://pieterjavanbeek.hyves.nl/>
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
 *
 * $Id: dav_precondition.php 170 2011-01-19 14:15:53Z kobasoft $
 **************************************************************************/

/**
 * File documentation (who cares)
 * @package DAV_Server
 */

/**
 * Precondition submitted by the user through the If: header.
 * @package DAV_Server
 */
class DAV_Precondition {
    
  
  private $resource;
  private $etag;
  private $locks;
  
  
  public function __construct($resource, $etag, $locks) {
    $this->resource = $resource;
    $this->etag     = $etag;
    $this->locks    = $locks;
  }
  
  
  //public function start() {
  //  return $this->resource->precondition_start(
  //    $this->etag, $this->locks
  //  );
  //}
  
  
  public function end() {
    $this->resource->precondition_end(
      $this->etag, $this->locks
    );
  }
  
  
} // class DAV_Precondition

