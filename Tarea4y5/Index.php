<?php
   session_start();
    function noError(){}
        set_error_handler("noError");
        
    include("lib/includes.php");


    $_SESSION["user"]; 
    $_SESSION["pass"];
    $_SESSION["name"];
        if($_POST){
            extract($_POST);

            $values =array('username'=>$username,'password'=>$password);

            $link =conexion::getInstancia();
            $id = $_POST['id']+0;
                    $rs= conexion::consulta("SELECT `username`,`password`,`nombre` FROM `admin`");
                    if(count($rs) > 0){
                        $values = $rs[0];
                        $entro=null;
                        foreach($rs as $info){
                   
                        if( $info['username'] == $username && $info['password'] == $password  ){
                            $_SESSION["user"] = $info['username'];
                            $_SESSION["pass"] = $info['password'];
                            $_SESSION["name"] = $info['nombre'];
                            $_SESSION['filtro'] = "`id` DESC";
                            $entro=true;
                            break;
                        }else { 
                            $entro=false;
                        }
                   }
                   if($entro==true){
                    ?><script>window.addEventListener("load", function(evt) {M.toast({html: 'Bienvenido <?php echo("{$_SESSION['name']}")?>!'})})</script><?
                   }else if( $entro==false){
                    ?><script>window.addEventListener("load", function(evt) {M.toast({html: 'Credenciales incorrectas.'})})</script><?
                   }
                  
             }
        }else if(isset($_GET['cerrar'])){

                session_unset(); 
                session_destroy(); 
            }
///paginacion
            $obrasTotal =conexion::consulta("SELECT COUNT(*) FROM `obras` where `estado`= 'a'");
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

        $obrasShow=conexion::consulta("SELECT * FROM `obras` where `estado`= 'a' ORDER BY `id` DESC limit $limite OFFSET $offset ");
    
        $classBtn; ($page > 1)? $classBtn = "waves-effect":$classBtn = "disabled"; 
        $classBtn2; ($pages > 1  && $page!= $pages)? $classBtn2 = "waves-effect":$classBtn2 = "disabled"; 

        $media = ceil($pages/2);
    ?>
    <!DOCTYPE html>
    <html lan="es" dir="ltr">
    <head>
    <title>Tarea4y5</title>
    <meta charset="utf-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    </head>

    <body  >
    <header>
    <nav>
    <div  class="nav-wrapper blue-grey darken-1">
       
            <a href="Index.php" class="brand-logo fontSize elLogo"> CANDIDO BIDO SRL</a>
            <a href="#"  data-target="mobile-view" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <?php if(isset($_SESSION["user"])){?>
            <li><a class="waves-effect waves-light " href="Add.php">Administrar Obras</a></li>
            <li><a class="waves-effect waves-light " href="AddAdmin.php">Administrar Accesos</a></li>
            <li><a class="waves-effect waves-red modal-trigger" onclick="return confirm('Desea cerrar sesión?')"  href="Index.php?cerrar=0" > Cerrar Sesión   </a>  </li>
        
        <?php  }else{  ?>
            <li><a class="waves-effect waves-light modal-trigger " href="#modal1" > Iniciar Sesión   </a>  </li>
        <?php }?>
      </ul>
    </div>
  </nav>

   <ul class="sidenav" id="mobile-view">
   <?php if(isset($_SESSION["user"])){?>
            <li><a class="waves-effect waves-light " href="Add.php"><i class="material-icons left">create</i>Administrar Obras</a></li>
            <li><a class="waves-effect waves-light " href="AddAdmin.php"><i class="material-icons left">lock_open</i>Administrar Accesos</a></li>
            <li><a class="waves-effect waves-red modal-trigger" onclick="return confirm('Desea cerrar sesión?')"  href="Index.php?cerrar=0" ><i class="material-icons left">power_settings_new </i>Cerrar Sesión</a></li>
        
        <?php  }else{  ?>
            <li><a class="waves-effect waves-light modal-trigger " href="#modal1" > <i class="material-icons left">account_circle</i>Iniciar Sesión   </a>  </li>
        <?php }?>
    </ul>
  </header>
  <main>
<div id="miMainDiv" class="paging10">
    <div class="container"> 
      <h4>Obras.</h4>
    </div>
  
    <?php if (count($obrasShow) > 0) {
     foreach($obrasShow as $info){  ?>
         <div  class="elPoder2"> 
            <div  class=" card hoverable waves-effect waves-light modal-trigger" > 
                    <div    onclick="buenDisplay('images/<?php echo "{$info['image']}"?>','<?php echo "{$info['nombre']}"?>','<?php echo "{$info['descripcion']}"?>');"  class="card-image waves-block waves-light">
                        <img id="laImgD"  src="mthumb.php?src=/web/Tarea4y5/images/<?php echo "{$info['image']}"?>&w=230&h=180" >
                    </div>
                    <div class="elNameIndex card-content">
                        <span class=" grey-text text-darken-4"><?php echo "{$info['nombre']}"?></span>
                    </div>  
            </div>  
        </div>
     <?php } ?> 
      <?php } else { ?>
                    <span>No se encontraron obras por el momento.</span><br>
                    <img src="img/noRows.png"  height="200px" >
                <?php }?>
 </div>

<!--Modal para el logIn-->
    <div id="modal1" class="modal modalSize">
        <div class="modal-content ">
            <h5>Inicio de Sesión.</h5>
          <form   method="POST" action="">
                <div class="input-field ">
                    <i class="material-icons prefix">verified_user</i>
                    <input name="username" id="input_text" type="text"   class="validate"   required autocomplete="off" >
                    <label for="input_text">Nombre de Usuario</label>
                </div>
                <div class="input-field">
                    <i class="material-icons prefix">lock</i>
                    <input name="password"  id="password" type="password" class="validate" required autocomplete="off" >
                    <label for="input_text">Contraseña</label>
                </div>
                <div class="input-field ">
                    <p onchange="elShow();" >
                        <label> <input type="checkbox"  id="mostrarPass" class="filled-in " /><span>Mostrar Contraseña</span></label>
                    </p>
                </div>
                <div >
                    <button class="btn waves-effect waves-light  blue-grey darken-1 center" id="ingresar" type="submit"  name="action">Ingresar
                        <i class="material-icons right">add</i>
                    </button>
                </div>
        </form>
      </div>
    </div>

<!--Modal para las imagenes-->
    <div id="modal3" class="modal">
        <div class="modal-content center-align">
            <h5 id="nombreModal"></h5>       
            <img id="imgModal" src=""  class="materialboxed imgCenter">
        </div>
        <div class="modal-footer">
            <h6 id="descripModal"></h6>
        </div>
    </div>
</main>
<footer class="page-footer white">
        <div class="container center">
        <?php if (count($obrasShow) > 0) { ?>
            <ul class="pagination">
                <li class="<?php echo"{$classBtn}"?>" ><a href="?page=<?php echo"{$page}"- 1 ?>"><i class="material-icons">chevron_left</i></a></li>
                <li class="active"><a href="?page=1"> <?php echo"{$page}"?> </a></li>
                <li class=""><a href="#!"> De </a></li>
                <li class="active"><a href="?page=<?php echo"{$pages}"?>"> <?php echo"{$pages}"?> </a></li>
                <li class="<?php echo"{$classBtn2}"?>" ><a href="?page=<?php echo"{$page}"+ 1 ?>"><i class="material-icons">chevron_right</i></a></li>
            </ul>    
        <?php } ?>
        </div>
    </footer>

<script type="text/javascript">
    M.AutoInit();

    var tipo =  document.getElementById('password');
     function elShow(){
         if(tipo.type == "password"){
            tipo.type ="text";
         }
         else if(tipo.type == "text"){
            tipo.type ="password"; 
         }
        }

function buenDisplay(img,name,description){

    var modalImg = document.getElementById("modal3");
    modalImg.style ="width: 75% !important ; max-height: 85% !important ; overflow-y: hidden !important ;";
    var instance = M.Modal.getInstance(modalImg);

    document.getElementById('nombreModal').innerHTML = name;
    document.getElementById('imgModal').src = img;
    document.getElementById('descripModal').innerHTML = description;

    instance.open();
}
 document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems, options);
  });

  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.materialboxed');
    var instances = M.Materialbox.init(elems, options);
 
  });

    var ids = [];
    const listado =document.getElementById("hoverMe");
    listado.addEventListener("over",  listado.cursor = pointer);

    function agregar(id,tipo) {
        if(ids.includes(id) == true){
            var index = ids.indexOf(id);
            if (index > -1) {
                ids.splice(index, 1);
                document.getElementById(id+tipo).className = "list-group-item";
                document.getElementById(id +tipo+'c').checked = false;
            }
        }else{
            ids.push(id);
            document.getElementById(id +tipo).className = "list-group-item active";
            document.getElementById(id +tipo+'c').checked = true;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems, options);
  });

</script>
</body>
</html>
