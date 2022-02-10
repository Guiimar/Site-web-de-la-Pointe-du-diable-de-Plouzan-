<<?php
    $url = "http://api.openweathermap.org/data/2.5/weather?q=Plouzan%C3%A9&lang=fr&units=metric&appid=aae513c1c6cd6f922ced80ef0faade0e";

    $file = file_get_contents($url);

    $json = json_decode($file);

    #var_dump($json);

    # Récupération et conversion des données météorologiques
    $name = $json->name;
    $meteo = $json -> weather[0] -> main;
    $tempC = $json -> main -> temp;
    $wind = $json -> wind -> speed ;
    $windkmh = $wind*3.6;
    $humidity = $json -> main -> humidity;
    $sunrise = $json -> sys -> sunrise;
    $sunset = $json -> sys -> sunset;
    $description = $json -> weather[0] -> description; 
    $id = $json -> weather[0] -> id;
    $icon = $json -> weather[0] -> icon;

    ### Conversion du lever du soleil
    $dtL = new DateTime('@' . $sunrise);
    $dtL->setTimezone(new DateTimeZone('Europe/Paris'));
    ### Conversion du coucher du soleil
    $dtC = new DateTime('@' . $sunset);
    $dtC->setTimezone(new DateTimeZone('Europe/Paris'));


    ### Variables Seeing
    $Tseuil = 4 ; 
    $Vseuil = 4 ;
    $Hseuil = 75 ;    
   
    
    function testConditions($seeing,$tempC,$wind,$humidity){
        global $Tseuil,$Vseuil,$Hseuil;
        if ($tempC > $Tseuil){
            $seeing=$seeing-1;
        }
        if ($wind >$Vseuil ){
            $seeing=$seeing-1;
        }
        if ($humidity >$Hseuil ){
            $seeing=$seeing-1;
        }

        if ($seeing<1 ){
            $seeing = 1;
        }
        echo $seeing; 

    }
    ### Seeing
    function seeing($tempC,$meteo,$wind,$humidity,$description,$id) {

        global $Tseuil,$Vseuil,$Hseuil,$sunrise,$sunset ;
        
        
       
        

        if ($meteo == "Clear" ){
            $seeing = 9;
            testConditions($seeing,$tempC,$wind,$humidity);
        }
        if ($meteo == "Clouds"){
            $seeing = 6;
            if ($description =="scattered clouds"){
                $seeing=$seeing-1;
            }
            if ($description =="broken clouds"){
                $seeing=$seeing-2;
            }
            if ($description =="overcast clouds"){
                $seeing=$seeing-4;
            }
            testConditions($seeing,$tempC,$wind,$humidity);
        }
        if ($meteo == "Rain"){
            $seeing = 6;
            if ($id == 501 or $id == 520 or $id == 521 ){
                $seeing=$seeing-2;
            }
            if ($id == 502 or $id == 503 or $id == 504 or $id == 511 or $id == 522 or $id == 531 )
            { $seeing=$seeing-4;  
            }
            testConditions($seeing,$tempC,$wind,$humidity); 

        }
        if ($meteo == "Drizzle"){
            $seeing = 6;
            if ($id == 301 or $id == 311 or $id == 321 ) {
                $seeing=$seeing-2;
            }
            if ($id == 302 or $id == 312 or $id == 313 or $id == 314 )
            { $seeing=$seeing-4;  
            }
            testConditions($seeing,$tempC,$wind,$humidity); 

        }
        if ($meteo == "Snow"){
            $seeing = 6;
            if ($id == 601 or $id == 611 or $id == 612 or $id == 615 or $id == 620 ) {
                $seeing=$seeing-1;
            }
            if ($id == 613 or $id == 616 or $id == 621) {
                $seeing=$seeing-2;
            }
            if ($id == 602 or $id == 622)
            { $seeing=$seeing-4;  
            }
            testConditions($seeing,$tempC,$wind,$humidity);

        }
        if ($meteo == "Thunderstorm"){
            $seeing = 2;
            if ($id == 210) {
                $seeing=$seeing+1;
            }
            testConditions($seeing,$tempC,$wind,$humidity); 

        }
        if ($meteo == "Atmosphere"){
            $seeing = 2;
            testConditions($seeing,$tempC,$wind,$humidity); 

        }
        return;
    }
    
    #Affichage du seeing
    #echo "Seeing : ";
    #seeing($tempC,$meteo,$wind,$humidity,$description,$id);
    #echo "<br>";


    ## Affichage des conditions météorologiques
    #echo "Ville : ". $name. "<br>";
    #echo "Météo : ". $meteo. "<br>";
    #echo "Description : ". $description. "<br>";
    #echo "Date : " . date("d/m/Y") . "<br>";
    date_default_timezone_set("Europe/Paris");
    #echo "Heure :  " . date("H:i:s")."<br>";
    #echo "Température  : " . $tempC." °C ". "<br>" ;
    #echo "Vent : ". $wind." m/s"."<br>";
    #echo "Humidité : ".$humidity. "%"."<br>";
    #echo "Lever du soleil : ".$dtL->format('H:i')."<br>";
    #echo "Coucher du soleil : ".$dtC->format('H:i')."<br>";
    

    

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Rafraichissement de la page toutes les 60 secondes -->
        <meta http-equiv="refresh" content="60">
        
        <link rel="stylesheet" href="stylebandeau.css" />
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        <link rel="stylesheet" href="styleimage.css" />

        <title>Skywatch</title>
        <!-- style météo -->
        
    </head>
    <body>
    <!-- bandeau -->
    <header role="header">
        <nav class="menu" role="navigation">
            <div class="inner">
                <div class="m-left">
                    <a href="AVI/last_AVI_sequence.avi" class="link"><i class="fas fa-video"></i>  <i class="fas fa-hourglass-half"></i> Timelapse de la dernière heure</a>
                    <a href="AVI/last_AVI_seq_Night.avi" class="link"><i class="fas fa-video"></i>  <i class="fas fa-moon"></i> Timelapse de la dernière nuit</a>
                    <i class="fad fa-webcam"></i>
                    <i class="fad fa-webcam-slash"></i>
                </div>
                <div class="m-right">             
                    <img src ="https://www.astronomie-pointedudiable.fr/wp-content/uploads/2015/03/logo-gens-de-la-lune.png" class="logo" alt="logo gens de la lune">
                    <img src = "https://www.imt-atlantique.fr/sites/default/files/Images/Ecole/charte-graphique/IMT_Atlantique_logo_RVB_Baseline_400x272.jpg" alt="Logo de l'école IMT Atlantique" class="logo" >
                </div>
            </div>
        </nav>
    </header>
    <!-- icône météo -->
    <div class="main">
    <div class="photo">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
            <section id="c">
	            <!-- h2> Sky Watch Alphea 6CL </h2 -->
	            <img src="" id="image_skywatch" alt="image Skywatch" class="skywatch"></div>
            </section>

            <script type="text/javascript">
	            function load_image(){
		            var image  = document.getElementById('image_skywatch');
		
		            $.ajax({
		                url: 'get_image.php',
		                type: 'GET',
		                dataType: "json",
		                contentType: "application/json",
		                success: function (data) {
			                image.src  = data;
		                }
		            });
	            }
	            load_image();
	            intervalID = setInterval(load_image, 20000);
		
                
        </script>
    </div>
    <div class="meteo">
    <table class="table">
    <!-- tableau météo -->
  <tr>
    <th>Ville : </th>
    <td><?php echo $name ?></td>
  </tr>
  <tr>
    <th>Météo : </th>
    <td><?php echo $meteo ?></td>
  </tr>
  <tr>
    <th>Description : </th>
    <td class="caseicone"><?php echo $description ?> <img src = "http://openweathermap.org/img/wn/<?php echo $icon ?>@2x.png" class="icone"> </td>
  </tr> 
  <tr>
    <th>Date : </th>
    <td><?php echo date("d/m/Y") ?></td>
  </tr>
  <tr>
    <th>Heure : </th>
    <td><?php echo date("H:i:s") ?></td>
  </tr>
  <tr>
    <th>Température : </th>
    <td><?php echo $tempC ?> °C</td>
  </tr>
  <tr>
    <th>Vent : </th>
    <td><?php echo $windkmh ?> km/h</td>
  </tr>
  <tr>
    <th>Humidité : </th>
    <td><?php echo $humidity ?> %</td>
  </tr>
  <tr>
    <th>Lever du soleil : </th>
    <td><?php echo $dtL->format('H:i') ?></td>
  </tr>
  <tr>
    <th>Coucher du soleil : </th>
    <td><?php echo $dtC->format('H:i') ?></td>
  </tr>
  <tr>
    <th>Qualité du ciel (1 à 9): </th>
    <td><?php seeing($tempC,$meteo,$wind,$humidity,$description,$id); ?></td>
  </tr>
</table>
<a target="blank" style="text-decoration:none;" href="http://www.calendrier-lunaire.net/" class="image-lune"><img src="http://www.calendrier-lunaire.net/module/LYWR2YW5jZWQtMTI0LWgyLTE2MjAxOTk0OTAuODIxNy0jMTUwOTFmLTMwMC0jZGVlYWI0LTE2MjAxOTk0OTAtMS0xMA.png" alt="La Lune" title="La Lune" /></a>
    </div>
    </div>
    <p>Barre de recherche</p>
   
    <form action="form.php" method ="GET" >
        <label for="start">Date:</label>
        <input type="date" id="date" name="date1"
       min="2021-03-01" max="2032-12-31">
       <input type="time" id="time" name="heure">

    <div>
    <input type="submit">
    <input type="reset">
    </div>
    </form>
    
    </body>
</html>