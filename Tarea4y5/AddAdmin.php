<?php

function noError(){}
  set_error_handler("noError");
  session_start();
include("lib/includes.php");

if (isset($_SESSION["user"])){
   
}else{
    echo "<script> window.location = './' </script> ";
}

$values = array('nombre'=>'','username'=>'','password'=>'','passwordVer'=>'');
 $titulo="Agregar Administrador:";
   /////CRUD/////
    if($_POST){
        $values = $_POST;
        extract($_POST);
        $link =conexion::getInstancia();
        $id = $_POST['id']+0;
/////////////Editar
        if($id > 0){

     
/////////////Insertar
        }else{
        if($password ==  $passwordVer){
            $sql="INSERT INTO `admin` ( `nombre`, `username`, `password`)VALUES(?,?,?)";
            $stmt =mysqli_prepare($link,$sql);
            mysqli_stmt_bind_param($stmt,'sss', $nombre,$username,$password );
            mysqli_stmt_execute($stmt);
            unset($values);
            ?><script>window.addEventListener("load", function(evt) {M.toast({html: 'Admin agregado!'})})</script><?
        }else{
            ?><script>window.addEventListener("load", function(evt) {M.toast({html: 'Contraseña y verificacion diferentes!'})})</script><?
        }
    }

    }else{
        if(isset($_GET['editar'])){
            $titulo="Editar Administrador:";
        }
        else if(isset($_GET['desactivar'])){
           
        }
    }
?>
<!DOCTYPE html>
<html lan="es" dir="ltr">
<head>
<title>Tarea 4&5</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
</head>
<style>

</style>
<body >
<header>
        <nav>
            <div class=" nav-wrapper blue-grey darken-1">
                <a href="Index.php"  class="brand-logo fontSize elLogo"> CANDIDO BIDO SRL</a>
                <a href="#" data-target="mobile-view" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="waves-effect waves-light " href="./">Vista Usuario</a></li>
                    <li><a class="waves-effect waves-light " href="Add.php">Administrar Obras</a></li>
                    <li><a class="waves-effect waves-light " href="AddAdmin.php">Administrar Accesos</a></li>
                    <li><a class="waves-effect waves-red modal-trigger" onclick="return confirm('Desea cerrar sesión?')"  href="Index.php?cerrar=0" > Cerrar Sesión   </a>  </li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-view">
            <li><a class="waves-effect waves-light " href="./"><i class="material-icons left">pageview</i>Vista Usuario</a></li>
            <li><a class="waves-effect waves-light " href="Add.php"><i class="material-icons left">create</i>Administrar Obras</a></li>
            <li><a class="waves-effect waves-light " href="AddAdmin.php"><i class="material-icons left">lock_open</i>Administrar Accesos</a></li>
            <li><a class="waves-effect waves-red modal-trigger" onclick="return confirm('Desea cerrar sesión?')"  href="Index.php?cerrar=0" ><i class="material-icons left">power_settings_new </i>Cerrar Sesión</a></li>
        </ul>
    </header>
<main> 
<div class="container"> 
    <h4><?php echo($titulo);?></h4> 
        <form  class="grid-example col s4" method="POST" action="AddAdmin.php">
            <div class="input-field col s12">
                <input type="text"  name="id" value="<?php echo $values['id'];?>" hidden >
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">account_circle</i>
                    <input name="nombre" id="S" type="text" class="validate"  value="<?php echo $values['nombre'];?>" data-length="10" required autocomplete="off" >
                    <label for="input_text">Nombre</label>
                </div>
            </div>
        <div class="row">
            <div class="input-field col s12">
                <i class="material-icons prefix">verified_user</i>
                <input name="username" id="input_text" type="text" class="validate"  value="<?php echo $values['username'];?>" data-length="10" required autocomplete="off" >
                <label for="input_text">UserName</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s6">
                <i class="material-icons prefix">lock</i>
                <input name="password"  id="password" type="password" class="validate" value="<?php echo $values['password'];?>" data-length="10" required autocomplete="off" >
                <label for="input_text">Contraseña</label>
            </div>
            <div class="input-field col s6">
                <i class="material-icons prefix">lock</i>
                <input name="passwordVer"  id="password2" type="password" class="validate" value="<?php echo $values['passwordVer'];?>" data-length="10" required autocomplete="off" >
                <label for="input_text"> Verificar Contraseña</label>
            </div>
        </div>
            <button class="btn waves-effect waves-light  blue-grey darken-1 " id="agregar" type="submit"  name="action">Agregar
                <i class="material-icons right">add</i>
            </button>
        </form>


<div class="responsive-table table-status-sheet">

    <table   >
        <thead>
            <tr>
                <th>Nombre</th>
                <th>UserName</th>
                <th>Contraseña</th>
            </tr>
            </thead>

            <tbody >
<?php
$obras =conexion::consulta("SELECT * FROM `admin` ORDER BY `id` DESC ");
foreach($obras as $info){  
 ?>
          <tr>
            <td><?php echo $info['nombre']; ?> </td>
            <td><?php echo $info['username']; ?></td>
            <td><?php echo $info['password']; ?></td>
          </tr>
       
<?php }?> 
   </tbody>
  </table>

         </div>  
         
    </div>
    </main>
<footer >
    Aqui puede crear los administradores.
 </footer>
 
<script type="text/javascript">
 M.AutoInit();
 
/////Permite en el select asignar valor a File input
 function fileValue() {

 document.getElementById("fileName").value = "images/<?php echo "{$values['image']}"?>";
    
}
/////////////cuando se edita imagen permite visaluizarla..posiblemente no se anecesario de logra cambiar el archivo
function imgEdit(){

  var output = document.getElementById('output_image');
  output.src = "images/<?php echo "{$values['image']}"?>";
  output.style = "height:200px"

}

function preview_image(event) 
{
 var reader = new FileReader();
 reader.onload = function()
 {
  var output = document.getElementById('output_image');
  output.src = reader.result;
  output.style = "height:200px"
 }
 reader.readAsDataURL(event.target.files[0]);
}

  function validateFileType(){

        var fileName = document.getElementById("fileName").value;
        var idxDot = fileName.lastIndexOf(".") + 1;
        var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
        var reader = new FileReader();
        var agregarD = document.getElementById("fileName");


        if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
            reader.onload = function()
                {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                output.style = "height:200px"
                }
                reader.readAsDataURL(event.target.files[0]);
        }else{
            reader.onload = function()
                {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                output.style.display = "none"; 
                }
                reader.readAsDataURL(event.target.files[0]);

            agregarD.value  = "";
            alert("Solo archivos de tipo jpg/jpeg y png permitidos!");
        }   
    }


 $(document).ready(function() {
    $('input#input_text, textarea#textarea2').characterCounter();
  });
  $('#textarea1').val('New Text');
  M.textareaAutoResize($('#textarea1'));

</script>

</body>
</html>
