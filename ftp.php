<!--formulaire host,user,pass de votre compte ftp-->

<table>
    <form method="POST">
    <tr>
    <td>
    host : <input type="text" name="host"/>
    </td>
    </tr>
    <tr>
    <td>
    User : <input type="text" name="user"/>
    </td>
    </tr>
    <tr>
    <td>
    Pass : <input type="text" name="pass"/>
    </td>
    </tr>
    <tr>
    <td>
    <input type="submit" value="connecter"/>
    </td>
    </tr>
    </form>
    </table>

<?php


if (isset($_POST['host']) and isset($_POST['user']) and  isset($_POST['pass'])) {
    $ftp_server = $_POST['host'];
    $ftp_user = $_POST['user'];
    $ftp_pass = $_POST['pass'];
 
    // Mise en place d'une connexion basique
    $conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
 
    // Tentative d'identification
    if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
        #echo "Connecté en tant que $ftp_user@$ftp_server<br/>";
        #echo "conn=".$conn_id;
		ftp_pasv($conn_id, true) ;

		// On récupère la liste des répertoires
        $directories = ftp_nlist($conn_id, "/web/skywatch/"); 
        $i=0;
		
		// Pour chaque répertoire
        foreach($directories as $directory){
			// Si le répertoire contient est de la forme yyyy-mm-dd (4digits-2digits-2digits)
			if (preg_match("/((\d{4})\-(\d{2})\-(\d{2}))/", $directory)) {
				// Alors on l'ajoute dans la liste
				$listDirectories[$i] = $directory;
				$i ++;
			}
		}
		// On trie la liste
		rsort($listDirectories);
		// Le répertoire le plus récent est le premier de la liste
		$directory=$listDirectories[0];
		#var_dump($directory);
		
		// On récupère la liste des fichiers du répertoire
        $files = ftp_nlist($conn_id, $directory); 
        $i=0;
		
		// Pour chaque fichier
        foreach($files as $file){	
		// Si le fichier ne contient pas le mot "unfold" et son format est ".jpg"
			if (!preg_match("/unfold/", $file) && preg_match("/\.(jpg)$/", $file)) {
				// Alors on l'ajoute dans la liste
				$listFiles[$i] = $file;
				$i ++;
			}
		}
		// On trie la liste
		rsort($listFiles);
		// L'image la plus récente est la premire de la liste
		$file=$listFiles[0];
		// On affcihe le nom de l'image
		#var_dump($file);
        #echo $file;
        #echo "<br>";
        $file2 = str_replace("/web","",$file);
        $file3 = "http://90.63.133.56".$file2;
		#echo "L'image la plus récente est ".$file;
        echo $file3;
		
    } else {
		echo "Connexion impossible en tant que $ftp_user";
    }
	//var_dump($listDirectory);
    ftp_close($conn_id);
}
     // Fermeture de la connexion


?>