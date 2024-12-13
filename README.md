sistema de logeo
tablero inicial caja superiores muestra el total de las ventas efectivo por dia, gastos, ventas creditos, ventas nequi
tiene un grafico de ventas sumando efectivo y nequi y resta los gastos
grafico de productos mas vendidos y un listado de los ultimos pordutos agregados
secion de usuarios:
permite agregar usuarios con ciertos roles y fotos, los muestra en una tabla donde indica nombre usuario foto perfil estado y se pueden modificar o eliminar mas agregar nuevos
secion de gastos:
tambien tiene una secion similar aplicando el crud
categorias lo mismo para agregarlos a los productos
productos despues sigo

nota de posibles errores con su soluction
si al crear un usuario nuevo o producto y no permite la imagen, se debe de activar en el php.ini 
Habilita la extensión GD en PHP:
Abre el archivo php.ini (lo encontrarás en la carpeta de configuración de tu servidor XAMPP, normalmente en C:\xampp\php\php.ini).
Busca la línea que dice ;extension=gd o ;extension=gd2.
Quita el punto y coma (;) al inicio de la línea para descomentarla, dejándola así:
ini
Copiar código
extension=gd
Reinicia XAMPP:
Después de guardar los cambios en el archivo php.ini, reinicia Apache desde el panel de control de XAMPP para aplicar la configuración.
Después de hacer esto, la función imagecreatefrompng() debería funcionar correctamente.

para que tome las impresiones se dbe de instalar las impresoras y configurar de forma virtual tambien

la direcion ip debe ser fija en caso tal se conectaran otros dispositivos al servidor 
recordar que la direcion de internet es con los digitos 8
si en caso tal no permite el servidor toca agregar la regla en el firewall

para los datos reales de las entas toca veririfcar la zona horaria del servidor que sea igual al equipo
por que si verificamos la zona horaria en un archivo 'la zona horaria: ' . date_default_timezone_get(); nos muestra por defecto echo la zona horaria: Europe/Berlin
toca cambiarla en el archivo php.in search this: date.timezone = America/Bogota

problema de impimir tickect y pdf tocaba actualizar la carpeta tcpdf exactamente el archivo tcpdf, tcpdf_barcodes_1d, tcpdf_static, tcpdf_images listo

el error despues de realizar venta si estaba en la linea 126 como mostraba el mensaje y se modifico la linea poniendo el - al final

la impresora par que sea modo bidirecional tiene que ser version -4 y no -3  en regedy se pude ver w+r y regedy

agregar esto a usuario para validar imagen 
if (isset($_POST["nuevoUsuario"])) {

    if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) &&
        preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoUsuario"]) &&
        preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoPassword"])) {

        /*=============================================
        VALIDAR IMAGEN
        =============================================*/

        $ruta = "";

        // Verifica si el archivo es válido y no está vacío
        if (isset($_FILES["nuevaFoto"]) && $_FILES["nuevaFoto"]["error"] == 0) {

            // Verifica si el archivo tiene contenido y si es una imagen válida
            if ($_FILES["nuevaFoto"]["tmp_name"] != "") {

                // Obtenemos las dimensiones de la imagen
                list($ancho, $alto) = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);

                // Establecemos el tamaño para redimensionar la imagen
                $nuevoAncho = 500;
                $nuevoAlto = 500;

                /*=============================================
                CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
                =============================================*/

                $directorio = "vistas/img/usuarios/" . $_POST["nuevoUsuario"];

                // Verifica si el directorio ya existe, si no, lo crea
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true); // El tercer parámetro permite crear subdirectorios
                }

                /*=============================================
                DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                =============================================*/

                if ($_FILES["nuevaFoto"]["type"] == "image/jpeg") {
                    // Procesa la imagen JPEG
                    $aleatorio = mt_rand(100, 999);
                    $ruta = $directorio . "/" . $aleatorio . ".jpg";

                    // Creamos la imagen a partir del archivo
                    $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                    // Guardamos la imagen redimensionada
                    imagejpeg($destino, $ruta);
                }

                if ($_FILES["nuevaFoto"]["type"] == "image/png") {
                    // Procesa la imagen PNG
                    $aleatorio = mt_rand(100, 999);
                    $ruta = $directorio . "/" . $aleatorio . ".png";

                    // Creamos la imagen a partir del archivo
                    $origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                    // Guardamos la imagen redimensionada
                    imagepng($destino, $ruta);
                }
            }
        }

        /*=============================================
        GUARDA LOS DATOS DEL USUARIO EN LA BASE DE DATOS
        =============================================*/

        $tabla = "usuarios";

        // Encriptamos la contraseña
        $encriptar = crypt($_POST["nuevoPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

        // Preparamos los datos para insertar en la base de datos
        $datos = array(
            "nombre" => $_POST["nuevoNombre"],
            "usuario" => $_POST["nuevoUsuario"],
            "password" => $encriptar,
            "perfil" => $_POST["nuevoPerfil"],
            "foto" => $ruta
        );

        // Insertamos los datos en la base de datos
        $respuesta = ModeloUsuarios::mdlIngresarUsuario($tabla, $datos);

        if ($respuesta == "ok") {
            echo '<script>
                swal({
                    type: "success",
                    title: "¡El usuario ha sido guardado correctamente!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){
                    if(result.value){
                        window.location = "usuarios";
                    }
                });
            </script>';
        }

    } else {
        echo '<script>
            swal({
                type: "error",
                title: "¡El usuario no puede ir vacío o llevar caracteres especiales!",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if(result.value){
                    window.location = "usuarios";
                }
            });
        </script>';
    }
}

