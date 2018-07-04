<?php

   function noError(){}
     set_error_handler("noError");

include("lib/includes.php");

$mensaje = "";


if($_POST){
    extract($_POST);
     $link = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS);
     $values =array('db'=>$DB_NAME,'host'=>$DB_HOST,'user'=>$DB_USER,'pass'=>$DB_PASS);
     if($DB_HOST != null &&  $DB_USER != null &&  $DB_NAME != null ){
     if($link){

             $sql = "CREATE DATABASE IF NOT EXISTS `{$DB_NAME}`";
            mysqli_query($link, $sql);
            mysqli_select_db($link, $DB_NAME);
          
            $sql ="CREATE TABLE IF NOT EXISTS `admin`  
            (`id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
            `nombre` varchar(50) NOT NULL,
            `username` varchar(50) NOT NULL,
             `password` varchar(50) NOT NULL) 
            ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";
            mysqli_query($link, $sql);

            $sql =" INSERT INTO `admin` (`id`, `nombre`, `username`, `password`) VALUES (1, 'root', 'admin', '3sf4c1l')";
            mysqli_query($link, $sql);

            $sql ="CREATE TABLE IF NOT EXISTS `obras`  
            (`id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
            `nombre` varchar(50) NOT NULL,
            `descripcion` varchar(150) NOT NULL,
            `estado` char(1) NOT NULL,
            `image` blob NOT NULL)
            ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";

            mysqli_query($link, $sql);

         $config = <<< CONF
        <?php
        define("DB_HOST","{$DB_HOST}");
        define("DB_USER","{$DB_USER}");
        define("DB_PASS","{$DB_PASS}");
        define("DB_NAME","{$DB_NAME}");
CONF;
        file_put_contents("lib/configx.php", $config);

        echo "<script type='text/javascript'>alert('Configuracion Exitosa!')</script>";
        echo "<script> window.location = './' </script> ";
        exit();
      }else{
        echo "<script type='text/javascript'>alert('Error en Configuracion!')</script>";
     }
    } else{
        echo "<script type='text/javascript'>alert('Llene los campos necesario!')</script>";
     }
  }

?>
<!DOCTYPE html>
<html lan="es" dir="ltr">
<head>
<title>Tarea4&5</title>
<meta charset="utf-8"  >
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
 
</head>
<style>

</style>
<body  >
<header> 
<nav>
    <div class="nav-wrapper blue-grey darken-1">
    <a href="Index.php"  class="brand-logo fontSize elLogo"> CANDIDO BIDO SRL</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">

      </ul>
    </div>
  </nav>
  </header>
<main>
<div class="container">
  <h4>Configuracion DB:</h4>

    <form  class="form" method="post" >
      <div class="input-field ">
        <i class="material-icons prefix">cloud_download</i>
        <input name="DB_HOST" id="S" type="text" class="validate"  value="<?php echo $values['host'];?>"  required autocomplete="off" >
        <label for="input_text">Nombre de Host</label>
      </div>
      <div class="input-field ">
        <i class="material-icons prefix">create_new_folder</i>
        <input name="DB_NAME" id="input_text" type="text" class="validate"  value="<?php echo $values['db'];?>"  required autocomplete="off" >
       <label for="input_text">Nombre de DB</label>
      </div>
      <div class="input-field ">
        <i class="material-icons prefix">account_circle</i>
        <input name="DB_USER" id="input_text" type="text" class="validate"   value="<?php echo $values['user'];?>" required autocomplete="off" >
        <label for="input_text">Nombre de Usuario</label>
      </div>
      <div class="input-field ">
        <i class="material-icons prefix">lock</i>
        <input name="DB_PASS" id="password" type="password" class="validate"  value="<?php echo $values['pass'];?>" autocomplete="off" >
        <label for="input_text">Clave de DB</label>
      </div>
      
        <button class="btn waves-effect waves-light  blue-grey darken-1 " id="agregar" type="submit"  
        >Configurar <i class="material-icons right">add</i>
        </button>  
     
    </form>
 </div>
 </main>
<footer class="center" >
        <span class="text-muted"><h5>Pagina de configuracion de Base de Datos y Tablas.</h5></span>
 </footer>

<script>
 M.AutoInit();
</script>
</body>
</html>
