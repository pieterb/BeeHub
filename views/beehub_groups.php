<?php
  /*
   * Available variables:
   * $groups     All members of this directory
   */
  // TODO custom settings to beehub.css or bootstrap.css
  // TODO netter maken, dit zorgt ervoor dat het er uitziet zoals ik
  // wil maar dit kan waarschijnlijk netter/anders
  $header = '
<style type="text/css">
  #joingroups, #membershiprequests {
    padding-left:30px;
  }

  .custom-accordion {
    border: 0px solid #E5E5E5 !important;
    border-style:dotted !important;
    border-top-width:1px !important;
    margin-bottom: 0px !important;
  }

  .custom-accordion-other-background{
    background-color: #E6E7E8 !important;
  }

  .custom-accordion:hover {
    background-color: #D1E2D3 !important;
    border-style:dotted !important;
  }

  .custom-accordion-inner {
    border-top: 1px solid #E5E5E5 !important;
  }

  .custom-accordion-active {
    background-color: #E8F1E9 !important;
    border: 1px solid #B9D3BA !important;
  }

  .custom-accordion-active:hover {
    background-color: #E8F1E9 !important;
    border-style:solid !important;
  }

  .admin{
    font-size: 12px !important;
    text-align: right;
  }

  .rightbutton{
    padding: 12px !important;
    text-align: right;
  }

  .custom-btn-primary {
    width: 130px !important;
  }

</style>
  ';
  require 'views/header.php';
?>

<ul id="beehub-top-tabs" class="nav nav-tabs">
  <li class="active"><a href="#panel-my" data-toggle="tab">My groups</a></li>
  <li><a href="#panel-all" data-toggle="tab">All groups</a></li>
  <li><a href="#panel-pending" data-toggle="tab">Pending requests</a></li>
</ul>

<div class="tab-content">

  <div id="panel-my" class="tab-pane fade in active">
    <div class="accordion" id="membershipgroups">
      <!--div class="accordion-group custom-accordion custom-accordion-other-background">
        <div class="accordion-heading">
          <div class="row-fluid">
            <div class="accordion-toggle span9" data-toggle="collapse" data-parent="#joingroups" href=#creategroup>
              <h5>Create new group</h5>
            </div>
          </div>
        </div>
        <div id="creategroup" class="accordion-body collapse">
          <div class="accordion-inner custom-accordion-inner">
            <div>
                Dit formulier moet nog anders
                <form action="<?= BeeHub::$CONFIG['namespace']['groups_path'] ?>" method="post">
                <div>Group name: <input type="text" name="group_name" /></div>
                <div>Display name: <input type="text" name="displayname" /></div>
                <div>Description: <input type="text" name="description" /></div>
                <div><input type="submit" value="Add" /></div>
              </form>
            </div>
          </div>
        </div>
      </div--><?php
        $i = 1;
        foreach ($groups as $group) :
          if ( $group->is_member() ) :
      ?><div class="accordion-group">
        <div class="accordion-heading">
          <div class="accordion-toggle" data-toggle="collapse" data-parent="#membershipgroups" href="#membershipgroups-<?= $i ?>">
            <table width="100%"><tbody><tr>
              <th align="left"><?= $group->prop_displayname() ?></th>
              <td align="right">
                <?php if ( $group->is_admin() ) : ?>
                <a href="<?= $group->path ?>" class="btn btn-info">Manage</a>
                <?php else : ?>
                <a href="<?= $group->path ?>" class="btn">Details</a>
                <?php endif; ?>

                <button type="button" name="leave" value="<?= $this->user_prop_current_user_principal() ?>" class="btn btn-danger">Leave this group</button>

              </td>
            </tr></tbody></table>
          </div>
        </div>
        <div id="membershipgroups-<?= $i ?>" class="accordion-body collapse">
          <div class="accordion-inner custom-accordion-inner">
            <?= $group->prop(BeeHub::PROP_DESCRIPTION) ?>
            <!--<br><br>
            Dit moet nog anders<br><br>
            <form id="membership_form_<?= $group->name ?>" method="post">
              <p>The following users requested for you to sponsor them:</p>
              <table>
                <thead>
                  <tr>
                    <th>user_name</th>
                    <th>Display name</th>
                    <th>Accept?</th>
                    <th>Delete?</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="member_row" id="/users/evert">
                    <td>evert</td>
                    <td>evert</td>
                    <td><a href="#" class="accept_link">Accept</a></td>
                    <td><a href="#" class="remove_link">Delete</a></td>
                  </tr>
                </tbody>
              </table>
              <p>The following users are member:</p>
              <table>
                <thead>
                  <tr>
                    <th>user_name</th>
                    <th>Display name</th>
                    <th>Admin?</th>
                    <th>Delete?</th>
                  </tr>
                </thead>
                <tbody id="current_members">
                  <tr class="member_row" id="/users/laura">
                    <td>laura</td>
                    <td>laura</td>
                    <td>jep <a href="#" class="demote_link">demote</a></td>
                    <td><a href="#" class="remove_link">Delete</a></td>
                  </tr>
                  <tr class="member_row" id="/users/niek">
                    <td>niek</td>
                    <td>Niek bosch</td>
                    <td>jep <a href="#" class="demote_link">demote</a></td>
                    <td><a href="#" class="remove_link">Delete</a></td>
                  </tr>
                  <tr class="member_row" id="/users/pieterb">
                    <td>pieterb</td>
                    <td>Pipo</td>
                    <td>jep <a href="#" class="demote_link">demote</a></td>
                    <td><a href="#" class="remove_link">Delete</a></td>
                  </tr>
                </tbody>
              </table>
            </form>-->
          </div>
        </div>
      </div>
      <?php
          $i = $i + 1;
        endif;
      endforeach;
      ?>
    </div>
  </div>
  <div id="panel-all" class="tab-pane fade">

  </div>
  <div id="panel-pending" class="tab-pane fade">
    <h3>Join groups</h3>
    <div class="accordion" id="joingroups">
      <div class="accordion-group custom-accordion custom-accordion-other-background">
        <div class="accordion-heading">
          <div class="row-fluid">
            <div class="accordion-toggle span9" data-toggle="collapse" data-parent="#joingroups" href=#invitation3>
              <h5>Request membership</h5>
            </div>
          </div>
        </div>
        <div id="invitation3" class="accordion-body collapse">
          <div class="accordion-inner custom-accordion-inner">
            <div id="membershiprequests">
              Hier moet je kunnen zoeken in de lijst
              <br>
              <br>
              <div class="span3">
                <input type="text" data-provide="typeahead">
              </div>
              <div class="span2">
                <button class="btn btn-primary" type="button" id="requestmembershipbutton">
                  Send request
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="accordion-group custom-accordion">
        <div class="accordion-heading">
          <div class="row-fluid">
            <div class="accordion-toggle span9" data-toggle="collapse" data-parent="#joingroups" href=#invitation1>
                <h5>Groepsnaam</h5>
            </div>
            <div class="span3 rightbutton">
              <button class="btn btn-primary custom-btn-primary" type="button" id="acceptinvitationbutton">Accept invitation</button>
            </div>
          </div>
        </div>
         <div id="invitation1" class="accordion-body collapse">
          <div class="accordion-inner custom-accordion-inner">
            Dit is de beschrijving van de group
          </div>
        </div>
      </div>
      <div class="accordion-group custom-accordion">
        <div class="accordion-heading">
          <div class="row-fluid">
            <div class="accordion-toggle span9" data-toggle="collapse" data-parent="#joingroups" href=#invitation2>
                <h5>Groepsnaam 2 (waiting for approval)</h5>
            </div>
            <div class="span3 rightbutton">
              <button class="btn btn-primary custom-btn-primary" type="button" id="cancelrequestinvitationbutton">Cancel request</button>
            </div>
          </div>
        </div>
         <div id="invitation2" class="accordion-body collapse">
          <div class="accordion-inner custom-accordion-inner">
            Dit is de beschrijving van de group
          </div>
        </div>
      </div>

    </div>
  </div>
  <div id="panel-all" class="tab-pane fade">
  </div>
</div>
<?php
  $footer='<script type="text/javascript" src="/system/js/groups.js"></script>';
  require 'views/footer.php';
?>
