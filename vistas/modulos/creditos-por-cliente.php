<!-- //en este apartado se pone los usuarios que no pueden ingresar aqui direcionandolos a inicio o pagina no encontrada -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
$clienteBuscado = isset($_GET["cliente"]) ? $_GET["cliente"] : '';
?>
<div class="content-wrapper" style="background-image: url('vistas/img/plantilla/second.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

                  <section class="content-header" style="color: black"> 
                      <h1>Buscar Deudas de Clientes</h1>
                      <ol class="breadcrumb">
                        <li><a href="inicio" style="color: black"><i class="fa fa-dashboard"></i> Inicio</a></li>
                        <li class="active" style="color: black">Deudas de Clientes</li>
                      </ol>
                    </section>

          <section class="content">

             <div class="box">
                         <!-- primer header con los botones agregar y formulario -->
                        <div class="box-header with-border">
  
                        <a href="crear-venta">

                          <button class="btn btn-primary">
            
                            Agregar venta

                          </button>

                        </a>

                        <a href="ventas-credito">

                          <button class="btn btn-success">
            
                            Ver creditos

                          </button>

                        </a>

                      <!-- formulario inicial to look for client -->
                        <div class="box-body">

                              <form method="get" action="index.php">
                              <input type="hidden" name="ruta" value="creditos-por-cliente">

                            <div class="row">

                            <div class="col-md-4">
                               <label style="color: black;">Nombre del Cliente</label>
                               <input type="text" name="cliente" class="form-control" placeholder="Buscar Cliente..." value="<?php echo $clienteBuscado; ?>" required>
                            </div>

                            <div class="col-md-2" style="margin-top: 25px;">
                              <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                            </div>

                            </div>
                              </form>
                         </div>
                          </div>

                <div class="box-body">

                  <?php
                         $alerta = '';

                         if (isset( $_GET["cliente"]) && $_GET["cliente"] !== "") {

                
                           $clienteBuscado = htmlspecialchars(trim($_GET["cliente"]));

                           try {

                           $ventas = ControladorVentas::ctrVentasCreditos();

                           if (empty($ventas)) {
                            $alerta= '<script>
                            
                                    Swal.fire({
                                      icon: "warning",
                                      title: "Sin créditos",
                                      text: "No se encontraron ventas a crédito."
                                    });
                                    
                                  </script>';
                        } else {
                           $encontrados = [];

                           foreach ($ventas as $venta) {

                            $cliente = ControladorClientes::ctrMostrarClientes("id", $venta["id_cliente"]);
                                 $nombreCliente = $cliente["nombre"]; // suponiendo que en clientes tienes "nombre"

                                 if (stripos($nombreCliente, $clienteBuscado) !== false) {
                                   $encontrados[] = [
                                     "cliente" => $nombreCliente,
                                     "total" => $venta["total"],
                                     "abonado" => $venta["monto_abonado"],
                                     "deuda" => $venta["saldo_pendiente"],
                                     "factura" => $venta["codigo"],
                                     "fecha" => $venta["fecha"]
                                   ];
                                 }
                           }

                           if (!empty($encontrados)) {

                            $totalFacturas = 0;
                            $totalAbonado = 0;
                            $totalDebe = 0; // Inicializa el total

                                   foreach ($encontrados as $item) {
                                    $totalFacturas += $item["total"];
                                    $totalAbonado += $item["abonado"];
                                    $totalDebe += $item["deuda"];
                                       }

                                       // Aquí recién calculas el total adeudado
                                       $totalDebe = $totalFacturas - $totalAbonado;
        
                                  echo  '<table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
                                               <thead>
         
                                                <tr>
           
                                                  <th style="width:10px">#</th>
                                                  <th>Cliente</th>
                                                  <th>Total Factura</th>
                                                  <th>Abonado</th>
                                                  <th>Deuda</th>
                                                  <th>Factura</th>
                                                  <th>Fecha</th> 
                                                </tr> 

                                               </thead>

                                                <tbody>';
                                                 
                                                foreach ($encontrados as $key => $item) {
                                                  echo '<tr>
                                                          <td>'.($key+1).'</td>
                                                          <td>'.$item["cliente"].'</td>
                                                          <td>$ '.number_format($item["total"], 2).'</td>
                                                          <td>$ '.number_format($item["abonado"], 2).'</td>
                                                          <td>$ '.number_format($item["deuda"], 2).'</td>
                                                          <td>'.$item["factura"].'</td>
                                                          <td>'.$item["fecha"].'</td>
                                                        </tr>';
                                                }
               
                                                  echo '</tbody> </table>' ;

                                                  echo '<div class="alert" style="background-color: #f44336; color: white; font-size: 18px;">
                                                        <strong>Total Facturado: $ '.number_format($totalFacturas, 2).'</strong> </div>
                                                        <div class="alert" style="background-color: #4CAF50; color: white; font-size: 18px;">
                                                            <strong>Total Abonado: $ '.number_format($totalAbonado, 2).'</strong>
                                                        </div>
                                                        <div class="alert" style="background-color: #2196F3; color: white; font-size: 18px;">
                                                        <strong>Total Adeudado: $ '.number_format($totalDebe, 2).'</strong>
                                                        </div> ';
                                                } else {
                                                  $alerta= '<script>
                                                        Swal.fire({
                                                          icon: "info",
                                                          title: "Sin coincidencias",
                                                          text: "No se encontró información del cliente buscado."
                                                        });
                                                      </script>';
                                                }

                                              }

                                            } catch (Exception $e) {
                                              $alerta= '<script>
                                              
                                                      Swal.fire({
                                                        icon: "error",
                                                        title: "Error",
                                                        text: "'.$e->getMessage().'"
                                                      });
                                            
                                                    </script>';
                                          }
                                      
                                      }

                                      if (!empty($alerta)) {
                                        echo $alerta;
                                              }
                                      
                        ?>
                    
                </div>

                </div>

           </section>

</div>





