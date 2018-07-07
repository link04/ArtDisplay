<?php

function noError(){}
    set_error_handler("noError");
  session_start();
include("lib/includes.php");
if (isset($_SESSION["user"])){
    if(time() >  $_SESSION['cierreDeSession']){
        session_unset(); 
        session_destroy(); 
       }
}else{
    echo "<script> window.location = './' </script> ";
}
$values = array('nombre'=>'','descripcion'=>'','estado'=>'','image'=>'','target'=>'');
    $titulo="Agregar Obra:";

   /////CRUD/////
    if(isset($_POST['main'])){
        
        extract($_POST);
        $link =conexion::getInstancia();
        $id = $_POST['id']+0;
/////////////Editar

        if($id > 0){

            $val =$_FILES['name'];

            $image = $_FILES['image']['name'];

            $target = "images/".basename($image);
            $sql= "UPDATE `obras` SET `nombre`= ?,`descripcion`= ?, `estado`= ?,image = ? WHERE id = ?";
            $stmt =mysqli_prepare($link,$sql);


            if($image == ""){
                mysqli_stmt_bind_param($stmt,'ssssi', $nombre,$descripcion,$estado, $poder ,$id);
                mysqli_stmt_execute($stmt);
                echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Obra Editada!'})})</script>");
            }else{
                $rs= conexion::consulta("SELECT `image` FROM `obras`");
                   
                $entro=null;
                foreach($rs as $info){
                    if( $info['image'] == $image  ) {
                        $entro=true;
                        break;
                     } else { 
                        $entro=false;
                     }
                }
            if($entro == true){
                $values = $_POST;
                echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Debe cambiar el nombre de la imagen!'})})</script>");

            }else if( $entro == false){
                mysqli_stmt_bind_param($stmt,'ssssi', $nombre,$descripcion,$estado, $image ,$id);
                mysqli_stmt_execute($stmt);
                echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Obra Editada!'})})</script>");
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $msg = "Exito";
                          }else{
                    $msg = "Error";
                }
            }
        }
    }else{
/////////////Insertar
            $msg = "";
            $image = $_FILES['image']['name'];
          
                    $rs= conexion::consulta("SELECT `image` FROM `obras`");
                   
                            $entro=null;
                            foreach($rs as $info){
                                if( $info['image'] == $image  ) {
                                    $entro=true;
                                    break;
                                 } else { 
                                    $entro=false;
                                 }
                            }
                        if($entro == true){
                            $values = $_POST;
                            echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Debe cambiar el nombre de la imagen!'})})</script>");
                        }else if( $entro == false){
                            
                            $target = "images/".basename($image);
                            $sql="INSERT INTO `obras` ( `nombre`, `descripcion`, `estado`,image)VALUES(?,?,?,?)";
                            $stmt =mysqli_prepare($link,$sql);
                            mysqli_stmt_bind_param($stmt,'ssss', $nombre,$descripcion,$estado,$image );
                            mysqli_stmt_execute($stmt);
                            echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Obra Agregada!'})})</script>");
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                                    $msg = "Exito";
                                }else{
                                    $msg = "Error";
                                }
                            }
               }
        }else if(isset($_POST['filtro'])){
        extract($_POST);

        if($filtrando  == "1"){
            $_SESSION['filtro'] = "`estado` ASC";
        }else if($filtrando  == "2"){
            $_SESSION['filtro'] = "`estado` DESC";
        }else if($filtrando  == "3"){
            $_SESSION['filtro'] = "`id` ASC";
        }else if($filtrando  == "4"){
            $_SESSION['filtro'] = "`id` DESC";
        }
    }else{
        if(isset($_GET['editar'])){
            $titulo="Editar Obra:";
            $id = $_GET['editar']+0;
            $rs = conexion::consulta("SELECT * FROM `obras` WHERE id = '{$id}'");
            if(count($rs) > 0){
                $values = $rs[0];
                $ultimate = $values['image'];
                echo("<script>window.addEventListener('load', function(evt) {

                    var output = document.getElementById('output_image');
                       output.src = 'images/{$values['image']}';
                       output.style = 'height:200px'; })
                    </script>");
            }
        }
        else if(isset($_GET['desactivar'])){
            $id = $_GET['desactivar']+0;

            $rs = conexion::consulta("SELECT `estado` FROM `obras` WHERE id = '{$id}'");
            if(count($rs) > 0){
                $values = $rs[0];
            if( $values['estado'] == 'a'){
                conexion::ejecutar("UPDATE `obras` SET `estado`= 'd'  WHERE id = '{$id}'");
                echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Obra Desactivada!'})})</script>");
            }else{
                conexion::ejecutar("UPDATE `obras` SET `estado`= 'a'  WHERE id = '{$id}'");
                echo("<script>window.addEventListener('load', function(evt) {M.toast({html: 'Obra Activada!'})})</script>");

              }
           }
          }else if(isset($_GET['eliminar']) && $_SESSION["user"] === 'link04' ){
            $id = $_GET['eliminar']+0;
            unlink('test.html');
            $rs = conexion::consulta("SELECT * FROM `obras` WHERE id = '{$id}' ");
            if(count($rs) > 0){
                $aBorrar= $rs[0]['image'];
                unlink("images/$aBorrar");
                conexion::consulta("DELETE FROM `obras` WHERE id = '{$id}'");
            }
          }
        }
        ///paginacion
        
        $obrasTotal =conexion::consulta("SELECT COUNT(*) FROM `obras` ");
        $total = $obrasTotal[0][0];
         
         $limite =10;
         $pages = ceil($total / $limite);
         // What page are we currently on?
        $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
            'options' => array(
                'default'   => 1,
                'min_range' => 1,
            ),
        )));
        $offset = ($page - 1)  * $limite; 
        $start = $offset + 1;
        $end = min(($offset + $limite), $total);
            // The "back" link
            $prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

    // The "forward" link
     $nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

      $obrasShow = conexion::consulta("SELECT * FROM `obras` ORDER BY {$_SESSION['filtro']} limit $limite OFFSET $offset ");
     
      $classBtn; ($page > 1)? $classBtn = "waves-effect":$classBtn = "disabled"; 
      $classBtn2; ($pages > 1  && $page!= $pages)? $classBtn2 = "waves-effect":$classBtn2 = "disabled"; 
?>
<!DOCTYPE html>
<html lan="es" dir="ltr">
<head>
<meta charset="utf-8" />
    <title>CandidoBido</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon" />

</head>

<body >
    <header>
        <nav>
            <div class=" nav-wrapper blue-grey darken-1">
                <a href="index.php"  class="brand-logo fontSize elLogo"> CANDIDO BIDO SRL</a>
                <a href="#" data-target="mobile-view" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="waves-effect waves-light " href="./">Vista Usuario</a></li>
                    <li><a class="waves-effect waves-light " href="add.php">Administrar Obras</a></li>
                    <li><a class="waves-effect waves-light " href="addAdmin.php">Administrar Accesos</a></li>
                    <li><a class="waves-effect waves-red modal-trigger" onclick="return confirm('Desea cerrar sesión?')"  href="index.php?cerrar=0" > Cerrar Sesión   </a>  </li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-view">
            <li><a class="waves-effect waves-light " href="./"><i class="material-icons left">pageview</i>Vista Usuario</a></li>
            <li><a class="waves-effect waves-light " href="add.php"><i class="material-icons left">create</i>Administrar Obras</a></li>
            <li><a class="waves-effect waves-light " href="addAdmin.php"><i class="material-icons left">lock_open</i>Administrar Accesos</a></li>
            <li><a class="waves-effect waves-red modal-trigger" onclick="return confirm('Desea cerrar sesión?')"  href="index.php?cerrar=0" ><i class="material-icons left">power_settings_new </i>Cerrar Sesión</a></li>
        </ul>
    </header>
    <main> 
    <div class="container">    
    <h4><?php echo($titulo);?></h4> 
    <div class="container forms">
                <form  method="POST"  action="add.php"  enctype="multipart/form-data">
                            <div class="">
                                    <input type="hidden" name="size" value="1000000">
                                    <input type="text"  name="id" value="<?php echo $values['id'];?>" hidden >
                                    <input type="text"  name="estado" value="a" hidden >  
                            </div>
                
                            <div id="formDiv" class="input-field" >
                                <i class="material-icons prefix">art_track</i>
                                <input name="nombre" id="input_text" type="text" value="<?php echo $values['nombre'];?>" maxlength="30" size="30" required autocomplete="off" >
                                <label for="input_text">Nombre</label>
                            </div>
                            <div id="formDiv" class="input-field ">
                                <i class="material-icons prefix">description</i>
                                <textarea name="descripcion" id="textarea2" class="materialize-textarea"  maxlength="150" size="150" required><?php echo $values['descripcion'];?></textarea>
                                <label for="textarea2">Descripción</label>
                            </div>
                
            
                            <div class="file-field "> 
                                <div class="btn  blue-grey darken-1">                  
                                    <span>Img</span>
                                    <input type="file" onchange="validateFileType()"  id="fileName" accept="image/*" name="image" >
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate"  value="<?php echo $values['image'];?>"  required placeholder="Obra" id="imageValue" name="poder" type="text">                     
                                    <img height="" id="output_image" />
                                </div>
                            </div>
                            <hr>
                

                        <div  id="formDiv" >
                            <button class="btn waves-effect waves-light  blue-grey darken-1 " id="agregar" type="submit" name="main">Guardar
                            <i class="material-icons right">add</i>
                            </button>
                            <button class="btn waves-effect waves-light  blue-grey darken-1 " > <a class="textDecoration" href="./Add.php">Cancelar</a> 
                            <i class="material-icons right">cancel</i>
                            </button>                                    
                        </div>
            
                    </form>
          <hr>
                    <form action="" method="POST" name="filtro">
                        <select name="filtrando">
                            <option value="" disabled selected>Ordenar Por</option>
                            <option value="1" > Estado Activo</option>
                            <option value="2" > Estado Inactivo</option>
                            <option value="3" > Mas Antiguas</option>
                            <option value="4" > Mas Recientes</option>
                        </select>
                        <button name="filtro" type="submit" class="btn waves-effect waves-light tooltipped blue-grey darken-1 "
                        data-position="bottom" data-tooltip="Filtrar" ><i class="material-icons right">filter_list</i>Filtrar </button>
                    </form>
                </div>
                
                 </div>  
       
    <div id="miMainDiv" class="paging10 ">
    <?php if (count($obrasShow) > 0) {
    foreach($obrasShow as $info){  ?>
        <div  class="elPoder2"> 
            <div  class=" card hoverable waves-effect waves-light modal-trigger" > 
                    <div    onclick="buenDisplay('images/<?php echo "{$info['image']}"?>','<?php echo "{$info['nombre']}"?>','<?php echo "{$info['descripcion']}"?>');"  class="card-image waves-block waves-light">
                        <img id="laImgD"  src="mthumb.php?src=/images/<?php echo "{$info['image']}"?>&w=230&h=110" >
                    </div>
                    <div  class="card-content">
                        <div class="elNameAdd" >
                            <p class=" grey-text text-darken-4"><?php echo "{$info['nombre']}"?></p><br>
                        </div>
                        <a href="add.php?editar=<?php echo "{$info['id']}"?>" class="btn-floating  tooltipped waves-effect waves-light btn-small yellow darken-1" data-position="bottom" data-tooltip="Editar" > 
                        <i class="material-icons">edit</i></a>
                        <?php if($_SESSION["user"] === 'link04') { ?>
                            <a href="add.php?eliminar=<?php echo "{$info['id']}"?>" class="btn-floating tooltipped waves-effect waves-light btn-small  red darken-1" data-position="bottom" data-tooltip="Eliminar" onclick="return confirm('Desea Eliminar esta obra?') "> 
                            <i class="material-icons">delete</i></a>
                        <?php } ?>
                        <?php if($info['estado'] == 'a' ){ ?>
                                <a href="add.php?desactivar=<?php echo "{$info['id']}"?>" class="btn-floating  tooltipped waves-effect waves-light btn-small grey lighten-1" data-position="bottom" data-tooltip="Desactivar" > 
                                <i class="material-icons left">visibility_off</i></a>
                            <?php }else{ ?>
                                <a href="add.php?desactivar=<?php echo "{$info['id']}"?>" class="btn-floating  tooltipped waves-effect waves-light btn-small green lighten-1" data-position="bottom" data-tooltip="Activar" > 
                                <i class="material-icons left">visibility</i></a>
                            <?php } ?>
                    </div>  
            </div>  
        </div>
    <?php } ?>
    </div>
     <!-- img view -->
     <div id="modal3" class="modal" >
        <div class="modal-content center-align">
            <h5 id="nombreModal"></h5>       
            <img id="imgModal" src=""  class="materialboxed imgCenter">
            <h6  id="descripModal"></h6>
        </div>
    </div>

</main>
       <footer class="page-footer white">
        <div class="container center">
            <ul class="pagination">
                <li class="<?php echo"{$classBtn}"?>" ><a href="?page=<?php echo"{$page}"- 1 ?>"><i class="material-icons">chevron_left</i></a></li>
                <li class="active"><a href="?page=1"> <?php echo"{$page}"?> </a></li>
                <li class=""><a href="#!"> De </a></li>
                <li class="active"><a href="?page=<?php echo"{$pages}"?>"> <?php echo"{$pages}"?> </a></li>
                <li class="<?php echo"{$classBtn2}"?>" ><a href="?page=<?php echo"{$page}"+ 1 ?>"><i class="material-icons">chevron_right</i></a></li>
            </ul>    
                <?php } else { ?>
                    <span>No se encontraron obras por el momento.</span><br>
                    <img src="img/noRows.png"  height="200px" >
                <?php }?>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
<script type="text/javascript">
 M.AutoInit();

window.addEventListener("load",selectPag());

function buenDisplay(img,name,description){

var modalImg = document.getElementById("modal3");
modalImg.style ="width: 75% !important ; max-height: 85% !important ; overflow-y: hidden !important ;";
var instance = M.Modal.getInstance(modalImg);

document.getElementById('nombreModal').innerHTML = name;
document.getElementById('imgModal').src = img;
document.getElementById('descripModal').innerHTML = description;

instance.open();

}

/////Permite en el editar asignar valor a File input
 function fileValue() {
 document.getElementById("fileName").value = "images/<?php echo "{$values['image']}"?>";
}
/////////////cuando se edita imagen permite visaluizarla..posiblemente no se anecesario de logra cambiar el archivo


   function selectPag(){
    var page = window.location.search;

    if(page ==""){
        var opcion = document.getElementById("?page=1");
        opcion.className ="active";
    }else{
        var opcion = document.getElementById(page);
        opcion.className ="active";
    }

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
                output.style = "height:200px";
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

 document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems, options);
  });

</script>

</body>
</html>
