var search_parameters = null;
var login_information = new Object();

function fetchUsers () {
	$('usersearch').options.add(new Option('Utilisateur', -1));  
	new Ajax.Request('users.php', {
	  method:'get',
	  onSuccess: function(transport){

	     var json = transport.responseText.evalJSON();
		var field = $('usersearch');
		for (var i = 0; i < json.users.length; i++) {
			field.options.add(new Option(json.users[i].login, json.users[i].user_id));
		}
	   }
	});
}

function showCreateForm () {
	$('snippletform_wrapper').show();
	$('createbutton_wrapper').hide();
}

function hideCreateForm () {
	$('snippletform_wrapper').hide();
	$('createbutton_wrapper').show();
}

function setPage (page) {
	search_parameters.page = page;
	showSnipplets();
}

function searchSnipplet () {
	search_parameters.search = $('searchform').search.getValue();
	search_parameters.page = 1;
	search_parameters.search_field = $F('fieldsearch');
	search_parameters.search_user_id = $F('usersearch');

	showSnipplets();
}

function clearSearch () {
	 $('searchform').search.setValue('');
	$('usersearch').options[0].selected = true;

	searchSnipplet();
}

function setOrder (column) {
	if (column == search_parameters.orderby) {
		if (search_parameters.orderby_way == 'ASC') {
			search_parameters.orderby_way = 'DESC';
		} else {
			search_parameters.orderby_way = 'ASC';
		}
	} else {
		search_parameters.orderby_way = 'ASC';
		search_parameters.orderby = column;
	}

	showSnipplets();
}

function showSnipplet (id) {
	var json2 = Object.toJSON(login_information);
	new Ajax.Request('json.php?action=details', {
	  method:'get',
	  parameters: {id: id, login: json2},
	  onSuccess: function(transport){

	     var json = transport.responseText.evalJSON();
		_updatePage(json);
	   }
	});
}

function showSnipplets () {
	var params = null;
	if (search_parameters != null) {
		params = Object.toJSON(search_parameters);
	}

	var json2 = Object.toJSON(login_information);

	new Ajax.Request('json.php?action=list', {
	  method:'get',
	  parameters: {parameters: params, login: json2},
	  onSuccess: function(transport){
		$('snippletlist_content').innerHTML = '';

	     var json = transport.responseText.evalJSON();
		_updatePage(json);
	   }
	});

}

function showUpdateForm () {
	$('snippletedit_wrapper').show();
	$('snippletdetails_wrapper').hide();
}
function hideUpdateForm () {
	$('snippletedit_wrapper').hide();
	$('snippletdetails_wrapper').show();
}

function _updatePage(json) {
	if (json.parameters != null) {
		search_parameters = json.parameters;
	}
	$('snippletedit_wrapper').hide();
	if (json.snipplets != null) {
		$('snippletlist_wrapper').show();
		_showSnipplets(json.snipplets, json.pagecount);
	} else {
		$('snippletlist_wrapper').hide();
	}
	if (json.snipplet != null) {
		$('snippletdetails_wrapper').show();
		_showSnipplet(json.snipplet);
	} else {
		$('snippletdetails_wrapper').hide();
	}
	_showErrors(json.errors);
	_showMessages(json.messages);

	hideCreateForm();
}

function _showErrors(errors) {
	if (errors.length == 0) {
		$('errors_wrapper').hide();
	} else {
		$('errors_wrapper').show();
		var html = '';
		for (var i = 0; i < errors.length; i++) {
			html += '<p>' + errors[i].message + '</p>';
		}
		$('errors_content').innerHTML = html;
	}
}

function _showMessages(messages) {
	if (messages.length == 0) {
		$('messages_wrapper').hide();
	} else {
		$('messages_wrapper').show();
		var html = '';
		for (var i = 0; i < messages.length; i++) {
			html += '<p>' + messages[i].message + '</p>';
		}
		$('messages_content').innerHTML = html;
	}
}

function _generateSortedHeader(name, column, width) {
	var html = '';
	html += '<th width="'+width+'%" onclick="setOrder(\'' + column + '\');">' + name;
	if (search_parameters != null && search_parameters.orderby == column) {
		if (search_parameters.orderby_way == 'ASC') {
			html += ' v';
		} else {
			html += ' ^';
		}
	}
	html += '</th>';
	return html;
}

function _showSnipplets(snipplets, pagecount) {
		
		$('snippletlist_content').innerHTML = '';
		var html = '<table>';
		html += _generatePageButtons(pagecount);
		
		html += '<tr>';
		html += _generateSortedHeader('ID', 'snipplet_id', 1);
		html += _generateSortedHeader('Description', 'description', 20);
		html += _generateSortedHeader('Snipplet', 'snipplet', 40);
		html += _generateSortedHeader('Mots clefs', 'keywords', 15);
		html += _generateSortedHeader('Date', 'date', 12);
		html += _generateSortedHeader('Utilisateur', 'login', 12);
		html += '</tr>';

		for (var i = 0; i < snipplets.length; i++) {
			var snipplet = snipplets[i];
			var author = 'Anonyme';
			if (snipplet.login != null) {
				author = snipplet.login;
			}
			html += '<tr class="clickable" onClick="showSnipplet(' + snipplet.snipplet_id + ');"><td>' + snipplet.snipplet_id + '</td><td>' + snipplet.description + '</td><td>' + snipplet.snipplet + '</td><td>' + snipplet.keywords + '</td><td>' + snipplet.date + '</td><td>' + author + '</td></tr>';
		}

		html += _generatePageButtons(pagecount);

		html += '</table>';
		$('snippletlist_content').innerHTML = html;
}

function _showSnipplet(snipplet) {
		
		$('snippletdetails_content').innerHTML = '';
		var html = '<table>';




		html += '<tr><td>';
		if (snipplet.login != null) {
			html += 'Posté par '+snipplet.login+' le '+ snipplet.date+' - <a href="?snipplet_id='+snipplet.snipplet_id+'">Lien</a>';
		} else {
			html += 'Posté le '+ snipplet.date+' - <a href="?snipplet_id='+snipplet.snipplet_id+'">Lien</a>';
		}
		html += '</td></tr>';
		html += '<tr><td><b>';
		html += snipplet.description;
		html += '</b></td></tr>';
		html += '<tr><td><div id="snipplet_content">';
		html += snipplet.snipplet.replace(/\n|\r/g, '<br />');
		html += '</div></td></tr>';
		html += '<tr><td>Mots clefs : <i>';
		html += snipplet.keywords;
		html += '</i></td></tr>';
		html += '<tr><td>Fichiers :<ul>';
		for (var i = 0; i < snipplet.files.length; i++) {
			var file = snipplet.files[i];
			html += '<li><a target="_blank" href="uploads/' + snipplet.snipplet_id + '/' + file + '">' + file + '</a></li>';
		}
		html += '</ul></td></tr>';
		html += '</table>';
		$('snippletdetails_content').innerHTML = html;

		$('snippleteditform').description.setValue(snipplet.description);
		$('snippleteditform').keywords.setValue(snipplet.keywords);
		$('snippleteditform').snipplet.setValue(snipplet.snipplet);
		$('snippleteditform').snipplet_id.setValue(snipplet.snipplet_id);
		uploader.setParams({
  		 id: snipplet.snipplet_id 
		});

}

function _generatePageButtons (pagecount) {
	html = '';
	html += '<tr><td colspan="6">';
	for (var i = 1; i <= pagecount; i++) {
		html += '<input type="button" onClick="setPage('+i+');" value="'+i+'"';
		if (i == search_parameters.page) {
			html += ' DISABLED';
		}
		html += ' />'
	} 
	html += '</td></tr>';
	return html;
}

function updateSnipplet () {
	var form = $('snippleteditform');
	var data = {description: form.description.getValue(), snipplet: form.snipplet.getValue(), keywords: form.keywords.getValue(), snipplet_id: form.snipplet_id.getValue() };
	var json = Object.toJSON(data);
	json = json.replace(/#/g, '%23');
	
	var json2 = Object.toJSON(login_information);

	new Ajax.Request('json.php?action=edit&snipplet='+json+'&login='+json2, {
	  method:'get',
	  onSuccess: function(transport){
		var json = transport.responseText.evalJSON();
		_updatePage(json);
	   }
	});
	
}

function addSnipplet () {
	var form = $('snippletform');
	var data = {description: form.description.getValue(), snipplet: form.snipplet.getValue(), keywords: form.keywords.getValue() };
	var json = Object.toJSON(data);
	json = json.replace(/#/g, '%23');
	
	var json2 = Object.toJSON(login_information);

	new Ajax.Request('json.php?action=create&snipplet='+json+'&login='+json2, {
	  method:'get',
	  onSuccess: function(transport){
		var json = transport.responseText.evalJSON();
		_updatePage(json);
	   }
	});
}

function firstRun () {
	$('password').observe('keypress', function(event){
	    if(event.keyCode == Event.KEY_RETURN) {
		startLogin();
		// stop processing the event
		//Event.stop(event);
	    }
	});
	startLogin();
	fetchUsers();
	$('login_wrapper').show();
	$('loggin_wait_wrapper').hide();
	$('logged_wrapper').hide();
	
	Event.observe($('pagelength_input'), 'change', function() {
		//alert ($('pagelength_input').getValue());
		search_parameters.pagelength = $('pagelength_input').getValue();
		showSnipplets();
	});

	
}

function startLogin () {
	var form = $('login_form');

	login_information.login = form.login.getValue();

	$('login_wrapper').hide();
	$('loggin_wait_wrapper').show();
	new Ajax.Request('login.php', {
	  method:'get',
          parameters: {login: form.login.getValue(), password: form.password.getValue()},
	  onSuccess: function(transport){
		var json = transport.responseText.evalJSON();
		if (json.connected == 'YES') {
			login_information.sid = json.sid;
			login_information.login = json.login;
			$('username').innerHTML = login_information.login;
			$('login_wrapper').hide();
			$('loggin_wait_wrapper').hide();
			$('logged_wrapper').show();
		} else {
		
			$('login_wrapper').show();
			$('loggin_wait_wrapper').hide();
			$('logged_wrapper').hide();
		}
	   }
	});
}

function Logout () {
	$('login_wrapper').show();
	$('logged_wrapper').hide();
	$('loggin_wait_wrapper').hide();
	login_information.sid = null;
}
