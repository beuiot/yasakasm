<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link href="css/fileuploader.css" rel="stylesheet" type="text/css">	
<link rel="stylesheet" type="text/css" href="css/style.css" />

<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/snipplet.js"></script>
<title>Yet Another Super Awsome Kick Ass Snipplet Manager</title>
</head>
<body>

<div id="login_wrapper">
<form id="login_form">
<table>

<tr><td>login</td><td><input type="text" name="login" /></td><td>password</td><td><input id="password" type="password" name="password" /></td><td><input type="button" onClick="startLogin();" value="Se connecter" /></td></tr>

</table>
</form>
</div>

<div id="loggin_wait_wrapper">Veuillez patienter...</div>

<div id="logged_wrapper">Bienvenue, <span id="username"></span>.<input type="button" onClick="Logout();" value="Se déconnecter" /></div>

<div id="title_wrapper">
<center>
<h1>YASAKASM</h1>
<h3>Yet Another Super Awsome Kick Ass Snipplet Manager</h3>
</center>
</div>

<div id="errors_wrapper">
<h1>Erreur</h1>
<div id="errors_content"></div>
</div>

<div id="messages_wrapper">
<div id="messages_content"></div>
</div>

<div id="createbutton_wrapper">
<input type="button" onClick="showCreateForm();" value="Ajouter un snipplet" />
</div>

<div id="snippletform_wrapper">
<h1>Ajouter un snipplet</h1>
<form id="snippletform">
<table>

<tr><td>Description</td><td><input type="text" name="description" /></td><td></td></tr>
<tr><td>Most clefs</td><td><input type="text" name="keywords" /></td><td></td></tr>
<tr><td colspan="3">
<textarea name="snipplet" rows="10" cols="70">
</textarea>
</td></tr>
<tr><td colspan="3"><input type="button" onClick="addSnipplet();" value="Ajouter" /><input type="button" onClick="hideCreateForm();" value="Annuler" /></td></tr>

</table>
</form>

</div>


<div id="snippletdetails_wrapper">
<h1>Snipplet</h1>
<div id="snippletdetails_content"></div>

<br />
<input type="button" onClick="showUpdateForm();" value="Modifier" />
<br />

<input type="button" onClick="showSnipplets();" value="Retour à la liste" />
</div>

<div id="snippletedit_wrapper">
<form id="snippleteditform">
<input type="hidden" name="snipplet_id" />
<table>

<tr><td>Description</td><td><input type="text" name="description" /></td><td></td></tr>
<tr><td>Most clefs</td><td><input type="text" name="keywords" /></td><td></td></tr>
<tr><td colspan="3">
<textarea name="snipplet" rows="10" cols="70">
</textarea>
</td></tr>
<tr><td colspan="3">
<div id="file-uploader">		
		<noscript>			
			<p>Please enable JavaScript to use file uploader.</p>
			<!-- or put a simple form for upload here -->
		</noscript>         
	</div>
<script src="js/fileuploader.js" type="text/javascript"></script>
    <script>
    	var uploader;
        function createUploader(){            
            uploader = new qq.FileUploader({
                element: document.getElementById('file-uploader'),
                action: 'upload.php',
                params: {
					id: 1
				},
                debug: true
            });           
        }
        
        // in your app create uploader as soon as the DOM is ready
        // don't wait for the window to load  
        window.onload = createUploader;     
    </script> 
</td></tr>
<tr><td colspan="3"><input type="button" onClick="updateSnipplet();" value="Mettre à jour" /><input type="button" onClick="hideUpdateForm();" value="Annuler" /></td></tr>

</table>
</form>
</div>

<div id="snippletlist_wrapper">
<div id="search_wrapper">

<h1>Snipplets</h1>

<form id="searchform">
<table>
<tr><td><input id="search" type="text" name="search" /></td><td><select id="fieldsearch" name="fieldsearch"><option value="keywords">Mots clefs</option><option value="description">Description</option><option value="snipplet">Contenu</option></select></td><td><select id="usersearch" name="usersearch"></select></td><td><input type="button" onClick="searchSnipplet();" value="Chercher" /></td><td><input type="button" onClick="clearSearch();" value="Annuler la recherche" /></td></tr>
</table>

</form>
</div>

<div id="snippletlist_content"></div>

</div>



<div id="pagelength_wrapper">
<form id="pagelength_form">
<p>Résultats par page : <select id="pagelength_input" name="pagelength">
  <option value="10">10</option>
  <option value="15">15</option>
  <option value="20">20</option>
  <option value="25">25</option>
  <option value="30">30</option>
</select></p>

</form>
</div>
<script type="text/javascript">
	firstRun();
<?php
if (isset($_GET['snipplet_id'])) {
	echo "showSnipplet(".$_GET['snipplet_id'].")";
} else {
	echo "showSnipplets();";
}
?>
</script>


</body>
</html>
