<?php

class conexion{

    static $instancia = null;
    private $dbLink;

    public static function getInstancia(){

        if(self::$instancia == null) {
            self::$instancia = new conexion();
        }
        return self::$instancia->dbLink;
    }

    public static function consulta($sql){
        $dbLink = self::getInstancia();
        $rs = mysqli_query($dbLink, $sql);
        $resultado = array();
        
        if(mysqli_num_rows($rs) > 0){
            while($fila = mysqli_fetch_array($rs)){
                $resultado[] = $fila;
            }
        }
        return $resultado;
    }

    public static function ejecutar($sql){
        $dbLink = self::getInstancia();
        $rs = mysqli_query($dbLink, $sql);
    }

   private function __construct(){
       $this->dbLink = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME) or 
       die ("<script> window.location = 'Install.php' </script> ");
    }

     function __destruct(){
    mysqli_close($this->dbLink);
    }

}