<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
include("inc/funciones.inc.php");
include("secure/ips.php");

$metodo_permitido = "POST";
$archivo = "../logs/log.log";
$dominio_autorizado = "localhost";
$ip = ip_in_ranges($_SERVER['REMOTE_ADDR'], $rango);
$txt_usuario_autorizado = "admin";
$txt_password_autorizada = "admin";

//verifica si la direccion de origen sea la autorizada
if(array_key_exists("HTTP_REFERER", $_SERVER)){
    //Viene de una pagina dentro del sistema
    if(strpos($_SERVER['HTTP_REFERER'], $dominio_autorizado) !== false){
            if($ip === true){
                //verifica si los datos del formulario fueron enviados
                if(array_key_exists("usuario", $_POST) && array_key_exists("password", $_POST)){
                    //verifica si los campos no estan vacios
                    if(!empty($_POST["usuario"]) && !empty($_POST["password"])){
                        //verifica si los datos son correctos
                        if($_SERVER["REQUEST_METHOD"] == $metodo_permitido) {

                            //LIMPIEZA DE VALORES DESDE EL FORMULARIO
                            $valor_campo_usuario = (   (array_key_exists("txt_user",$POST))? htmlspecialchars(stripslashes(trim($_POST["txt_user"])), ENT_QUOTES) : "" );
                            $valor_campo_password = (   (array_key_exists("txt_pass",$POST))? htmlspecialchars(stripslashes(trim($_POST["txt_pass"])), ENT_QUOTES) : "" );

                            //SE VERIFICA QUE LOS VALORES DE LOS CAMPOS NO SEAN DIFERENTES DE VACIOS
                            if( ($valor_campo_usuario!="" || strlen($valor_campo_usuario)>0) and ($valor_campo_password!="" || strlen($valor_campo_password)>0)){ 
                                // LAS VARIABLES SI TIENEN VALORES
                                $usuario = preg_match('/[a-zA-Z0-9]{1,10}+$/', $valor_campo_usuario); // SE VERIFICA CON UN PATRON SI EL VALOR DEL CAMPO "usuario" CUMPLE CON LA SCONFICIONES ACPETABLES(SE ACEPTAN NUMERO, LETRAS MAYUSCULAS Y LETRAS MINUSCULAS, EN UN MAXIMO DE 10 CARACTERES Y UN MINIMO DE UN CARACTER)
                                $password = preg_match('/[a-zA-Z0-9]{1,10}+$/', $valor_campo_password); // SE VERIFICA CON UN PATRON SI EL VALOR DEL CAMPO "usuario" CUMPLE CON LA SCONFICIONES ACPETABLES(SE ACEPTAN NUMERO, LETRAS MAYUSCULAS Y LETRAS MINUSCULAS, EN UN MAXIMO DE 10 CARACTERES Y UN MINIMO DE UN CARACTER)
                                
                                //SE VERIFICA QUE LOS RESULTADOS DEL PATRON SEAN EXCLUSIVAMENTE POSITIVOS O SASTIFACTORIOS
                                if($usuario !== false and $usuario !== 0 and $password !== false and $password !==0){
                                     //
                                    if($valor_campo_usuario === $txt_usuario_autorizado and $valor_campo_password === $txt_password_autorizada){
                                        echo("HOLA MUNDO");
                                        crear_editar_log($archivo,"EL CLIENTE INICIO SESION SATISFACTORIAMENTE",1,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                                    }else {
                                        // EL USUARIO NO INGRESO LAS CREDENCIALES CORRECTAS
                                        crear_editar_log($archivo,"CREDENCIALES INCORRECTAS ENVIADAS HACIA //$_SERVER[HTTP_POST]$_SERVER[HTTP_REQUEST_URI]",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                                        header("HTTP/1.1 301 Moved Permanently");
                                        header("Location: ../index.php?status=7");  
                                    }

                                }else{
                                    //LOS VALORES INGRESADOS EN LOS CAMPOS POSEEN CARACTERES NO SOPORTADOS
                                    crear_editar_log($archivo,"ENVIO DE DATOA DEL FORMULARIO CON CARACTERES NO SOPORTADOS",3,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                                    header("HTTP/1.1 301 Moved Permanently");
                                    header("Location: ../index.php?status=6");
                                }
                                

                            }else{
                                //LAS VARIABLES ESTAN VACIAS O NO TIENEN VALORES
                                crear_editar_log($archivo,"ENVIO DE CAMPOS VACIO AL SERVIDOR",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                                eader("HTTP/1.1 301 Moved Permanently");
                                header("Location: ../index.php?status=5");

                            }
                    }else{
                       //LAS VARIABLES ESTAN VACIAS O NO TIENEN VALORES
                    crear_editar_log($archivo,"ENVIO DE METODO NO AUTORIZADO",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../index.php?status=4");
                    }
                }else{
                    //LA DIRECCION IP NO ES AUTORIZADA
                    crear_editar_log($archivo,"DIRECCION IP NO AUTORIZADA",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../index.php?status=3");
                }
            }else{
                //EL REFERER DE DONDE VIENE LA PETICION NO ESTA AUTORIZADO
                crear_editar_log($archivo,"HA INTENTADO SUPLAMANTAR UN REFERER NO AUTORIZADO",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ../index.php?status=2");
            }
        }else{
            //EL USUARIO DIGITO LA URL DESDE EL NAVEGADOR SIN PASAR POR EL FORMULARIO
            crear_editar_log($archivo,"EL USUARIO HA INTENTADO INGRESAR AL SISTEMA DE UNA MANERA INCORRECTA",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ../index.php?status=1");
        }
    }
}
?>