"use strict";

if (nl === undefined) {
  var nl = {};
}
if (nl.sara === undefined) {
  nl.sara = {};
}
if (nl.sara.beehub === undefined) {
  nl.sara.beehub = {};
}
if (nl.sara.beehub.codec === undefined) {
  nl.sara.beehub.codec = {};
}

// Some static values change these also in /src/beehub.php
nl.sara.beehub.users_path            = "/system/users/";
nl.sara.beehub.groups_path           = "/system/groups/";
nl.sara.beehub.sponsors_path         = "/system/sponsors/";
nl.sara.beehub.forbidden_group_names = [
  "home",
  "system"
];

// in a anonymous function, to not pollute the global namespace:
(function () {
	var active;
	if ( RegExp('^/system/groups/[^/]*$').test( window.location.pathname ) ) {
		active = 'groups';
	} else if ( RegExp('^/system/docs.*$').test( window.location.pathname ) ) {
    active = 'docs';
  } else if ( RegExp('^/system/sponsors/[^/]*$').test( window.location.pathname ) ) {
    active = 'sponsors';
  } else if ( RegExp('^/system/users/[^/]+$').test( window.location.pathname ) ) {
		active = 'profile';
	} else if ( RegExp('^/system/$').test( window.location.pathname ) ) {
		active = 'beehub';
	} else if ( RegExp('^/system/users/$').test( window.location.pathname ) ) {
    active = 'signup';
  } else {
		active = 'files';
	}
	$('.navbar-fixed-top li#navbar-li-' + active).addClass('active');
})();

(function() {
  var notification_window = $('#notifications');
  var notification_counter = $('#notification_counter');

  /**
   * Returns the correct notification for a specific type
   */
  function create_notification(type, data) {
    var notification = $('<div class="notification_item well"></div>');
    var client = new nl.sara.webdav.Client();

    switch(type) {
      case 'group_invitation':
        notification.html('<div style="float:left">You are invited to join the group \'<span name="groupname"></span>\'</div><div style="float:right"><button class="btn btn-success">Join</button> <button class="btn btn-danger">Decline</button></div><div style="clear:both"></div>');
        $('span[name="groupname"]', notification).text(data.displayname);
        $('.btn-success', notification).click(function() {
          client.post(data.group, nl.sara.beehub.reload_notifications, 'join=1');
        });
        $('.btn-danger', notification).click(function() {
          client.post(data.group, nl.sara.beehub.reload_notifications, 'leave=1');
        });
        break;
      case 'group_request':
        notification.html('<div style="float:left"><span name="user_displayname"></span> (<span name="user_email"></span>) requests a membership of group \'<span name="group_displayname"></span>\'</div><div style="float:right"><button class="btn btn-success">Accept</button> <button class="btn btn-danger">Decline</button></div><div style="clear:both"></div>');
        $('span[name="user_displayname"]', notification).text(data.user_displayname);
        $('span[name="user_email"]', notification).text(data.user_email);
        $('span[name="group_displayname"]', notification).text(data.group_displayname);
        $('.btn-success', notification).click(function() {
          client.post(data.group, nl.sara.beehub.reload_notifications, 'add_members[]=' + data.user);
        });
        $('.btn-danger', notification).click(function() {
          client.post(data.group, nl.sara.beehub.reload_notifications, 'delete_members[]=' + data.user);
        });
        break;
      case 'sponsor_request':
        notification.html('<div style="float:left"><span name="user_displayname"></span> (<span name="user_email"></span>) requests requests membership of sponsor \'<span name="sponsor_displayname"></span>\'</div><div style="float:right"><button class="btn btn-success">Accept</button> <button class="btn btn-danger">Decline</button></div><div style="clear:both"></div>');
        $('span[name="user_displayname"]', notification).text(data.user_displayname);
        $('span[name="user_email"]', notification).text(data.user_email);
        $('span[name="sponsor_displayname"]', notification).text(data.sponsor_displayname);
        $('.btn-success', notification).click(function() {
          client.post(data.sponsor, nl.sara.beehub.reload_notifications, 'add_members[]=' + data.user);
        });
        $('.btn-danger', notification).click(function() {
          client.post(data.sponsor, nl.sara.beehub.reload_notifications, 'delete_members[]=' + data.user);
        });
        break;
    }

    return notification;
  }

  nl.sara.beehub.show_notifications = function(data) {
    notification_window.empty();

    if (data.length === 0) {
      notification_window.append('There are no notifications');
      notification_counter.html('0');
    }else{
      $.each(data, function(key, content) {
        notification_window.append(create_notification(content.type, content.data));
      });
      notification_counter.html($('.notification_item', notification_window).length.toString());
    }
  };

  nl.sara.beehub.reload_notifications = function() {
    $.getJSON('/system/notifications.php', nl.sara.beehub.show_notifications);
  };

  setInterval(nl.sara.beehub.reload_notifications, 60000);
})();

// If nl.sara.webdav.codec.GetlastmodifiedCodec is already defined, we have a namespace clash!

if (nl.sara.beehub.codec.Sponsor !== undefined) {
  throw new nl.sara.beehub.Exception('Namespace nl.sara.webdav.codec.Sponsor already taken, could not load JavaScript library for WebDAV connectivity.', nl.sara.webdav.Exception.NAMESPACE_TAKEN);
}

/**
 * @class Adds a codec that converts DAV: getlastmodified to a Date object
 * @augments nl.sara.webdav.Codec
 */
nl.sara.beehub.codec.Sponsor = new nl.sara.webdav.Codec();
nl.sara.beehub.codec.Sponsor.namespace = 'http://beehub.nl/';
nl.sara.beehub.codec.Sponsor.tagname = 'sponsor';

nl.sara.beehub.codec.Sponsor.fromXML = function(nodelist) {
  var node;
  for (var counter = 0; counter < nodelist.length; counter++) {
    node = nodelist.item(counter);
    if ( (node.nodeType === 1) && (node.namespaceURI === 'DAV:') && (node.localName === 'href') ) {
      var childnode = node.childNodes.item(0);
      if ((childnode.nodeType === 3) || (childnode.nodeType === 4)) { // Make sure text and CDATA content is stored
        return childnode.nodeValue;
      }else{ // If the node is not text or CDATA, then we don't parse a value at all
        return null;
      }
    }
  }
};

nl.sara.beehub.codec.Sponsor.toXML = function(value, xmlDoc){
  var cdata = xmlDoc.createCDATASection( value );
  // The server doesn't parse XML correctly: it expects the DAV: namespace to always use the D: prefix. When that's fixed, we can use the next line:
  //var href = xmlDoc.createElementNS( 'DAV:', 'href' );
  // But for now we need to do it with these 2 lines:
  var href = xmlDoc.createElement('D:href');
  href.setAttribute("xmlns:D","DAV:");
  // The next lines are again 'normal'
  href.appendChild( cdata );
  xmlDoc.documentElement.appendChild( href );
  return xmlDoc;
};

nl.sara.webdav.Property.addCodec(nl.sara.beehub.codec.Sponsor);

// End of file