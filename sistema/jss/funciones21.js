/*
   Para que el codigo JS funcione correctamente debe ser llamado al final del archivo HTML.
   Pero como nosotros lo estamos llamando al archivo JS al inicio de cada archivo php importandolo desde el archivo scrips.php
   este puede presentar algunos inconvenientes de a la hora de ser ejecutado. para solucionar esto, debemos llamar las funciones JS
   cuando todo el contenido HTML este listo, Por esa razon colocamos el codigo en la funcion ready el objeto documento.
*/
$(document).ready(function(){

   //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
   $("#foto").on("change",function(){
      var uploadFoto = document.getElementById("foto").value;
       var foto       = document.getElementById("foto").files;
       var nav = window.URL || window.webkitURL;
       var contactAlert = document.getElementById('form_alert');
       
           if(uploadFoto !='')
           {
               var type = foto[0].type;
               var name = foto[0].name;
               if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
               {
                   contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                   $("#img").remove();
                   $(".delPhoto").addClass('notBlock');
                   $('#foto').val('');
                   return false;
               }else{  
                       contactAlert.innerHTML='';
                       $("#img").remove();
                       $(".delPhoto").removeClass('notBlock');
                       var objeto_url = nav.createObjectURL(this.files[0]);
                       $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                       $(".upimg label").remove();
                       
                   }
             }else{
                alert("No selecciono foto");
               $("#img").remove();
             }              
   });

   $('.delPhoto').click(function(){
      $('#foto').val('');
      $(".delPhoto").addClass('notBlock');
      $("#img").remove();

      if($("#foto_actual") && $("#foto_remove")){
         $("#foto_remove").val("img_producto.png");
      }

   });


   // Modal Form Add Product
   //Elemento obtenido del archivo lista_producto.php
   $(".add_product").click(function(event){
      // El metodo event.preventDefault(); evita que mi pagina se recargue
      event.preventDefault();
      let producto = $(this).attr("product");
      let action = 'infoProducto';
      // console.log(typeof producto);

      $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action, producto:producto},

            success: function(response){
               // console.log(response);
               if(response != "error"){
                  let info = JSON.parse(response);
                  // console.log(info);

                  // $('#producto_id').val(info.codproducto);
                  // $('.nameProducto').html(info.descripcion);

                  $('.bodyModal').html('<form action="" method="post" name="form_add_product" id="form_add_product" onsubmit="event.preventDefault(); sendDataProduct();">'+
                  '<h1><i class="fa-solid fa-box-open" style="font-size:45pt;"></i> <br> Agregar producto</h1>'+
                  '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                  '<input type="number" name="cantidad" id="txtCantidad" placeholder="cantidad el producto" required > <br>'+
                  '<input type="text" name="precio" id="txtPrecio" placeholder="precio el producto" required >'+ 
                  '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required >'+
                  '<input type="hidden" name="action" value="addProduct" required >'+
                  '<div class="alert alertAddProduct"></div>'+
                  '<div class="botonesModal">'+
                      '<button type="submit" class="btn_new"><i class="fa-solid fa-cart-plus"></i> Agregar</button>'+
                      '<a href="#" class="btn_cancel closeModal" onclick="closeModal();"><i class="fa-solid fa-circle-xmark"></i> Cerrar</a>'+
                  '</div>'+
              '</form>');
               }
            },
            error: function(error){
               console.log(error);
            }
         });

      $(".modal").fadeIn();
   });

   // let boton = document.getElementsByClassName("add_product");
   // boton[0].addEventListener("click",()=>{
   //     console.log("this");
   // })

 
   // Modal Form delete Product
   //Elemento obtenido del archivo lista_producto.php
   $(".del_product").click(function(event){
      // El metodo event.preventDefault(); evita que mi pagina se recargue
      event.preventDefault();
      let producto = $(this).attr("product");
      let action = 'infoProducto';
      // console.log(typeof producto);

      $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action, producto:producto},

            success: function(response){
               // console.log(response);
               if(response != "error"){
                  let info = JSON.parse(response);
                  // console.log(info);

                  // $('#producto_id').val(info.codproducto);
                  // $('.nameProducto').html(info.descripcion);

                  $('.bodyModal').html('<form action="" method="post" name="form_del_product" id="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
                  '<h1><i class="fa-solid fa-box-open" style="font-size:45pt;"></i> <br> Eliminar producto</h1>'+
                  '<p>¿Estas seguro de eliminar este producto?</p>'+
                
                  '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                  
                  '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required >'+
                  '<input type="hidden" name="action" value="delProduct" required >'+
                  '<div class="alert alertAddProduct"></div>'+
                  '<div class="botonesModal">'+
                  
                      '<button type="submit" class="btn_new"><i class="fa-solid fa-cart-plus"></i> Eliminar</button>'+
                      '<a href="#" class="btn_cancel closeModal" onclick="closeModal();"><i class="fa-solid fa-circle-xmark"></i> Cancelar</a>'+
                  '</div>'+
              '</form>');
               }
            },
            error: function(error){
               console.log(error);
            }
         });

      $(".modal").fadeIn();
   });

   $('#search_proveedor').change(function (e){
      e.preventDefault();

      let sistema = getUrl();
      // alert(sistema);
      location.href = sistema + 'buscar_producto.php?proveedor='+$(this).val();
   });


   // Activar campos para resetear cliente
   $('.btn_new_cliente').click(function(e){
      e.preventDefault();
      $('#nom_cliente').removeAttr('disabled');
      $('#tel_cliente').removeAttr('disabled');
      $('#dir_cliente').removeAttr('disabled');
   
      $('#div_registro_cliente').slideDown();
      // console.log("Hola mundo");
   });

   // Buscar Cliente
   $("#nit_cliente").keyup(
      function(e){
         e.preventDefault();
         
         // let cl = this.val;
         let cl = $(this).val();
         let action = "searchCliente";
         
         $.ajax(
            {
               url: 'ajax.php',
               type: 'POST',
               // dataType: '',
               async: true,
               data:{action:action, cliente:cl},
   
               success: function(response){
                  // console.log(response);
                  if(response == 0){

                     $("#idcliente").val("");
                     $("#nom_cliente").val("");
                     $("#tel_cliente").val("");
                     $("#dir_cliente").val("");

                     $(".btn_new_cliente").slideDown();
                  }else{
                     // let data = JSON.parse(response);
                     let data = $.parseJSON(response);
                     // console.log(data);
                     $("#idcliente").val(data.idcliente);
                     $("#nom_cliente").val(data.nombre);
                     $("#tel_cliente").val(data.telefono);
                     $("#dir_cliente").val(data.direccion);

                     $(".btn_new_cliente").slideUp();

                     $("#idcliente").attr('disabled','disabled');
                     $("#nom_cliente").attr('disabled','disabled');
                     $("#tel_cliente").attr('disabled','disabled');
                     $("#dir_cliente").attr('disabled','disabled');

                     $("#div_registro_cliente").slideUp();
                  }
               },
               error: function(error){
                  console.log(error);
               }
            });
      }
   );

   // Crear cliente  desde la pestaña --ventas
  $("#form_new_cliente_venta").submit(function(e){
      e.preventDefault();
      let action = "addClient";
      $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action, datos:$("#form_new_cliente_venta").serialize()},

            success: function(response){
               // console.log(response);

               if(response != "error"){
                  alert("Cliente " + response + " Guardado correctamente");

                  $("#idcliente").val(response);
                  
                  $("#nom_cliente").attr('disabled','disabled');
                  $("#tel_cliente").attr('disabled','disabled');
                  $("#dir_cliente").attr('disabled','disabled');

                  // Ocultar el boton agregar
                  $(".btn_new_cliente").slideUp();

                  // Ocultar el boton guardar
                  $("#div_registro_cliente").slideUp();

               }else{
                  alert("Error al guardar el cliente");
               }
            },
            error: function(error){
               console.log(error);
            }
         });
    });

    //Buscar Producto
    $("#txt_cod_producto").keyup(function(e){

      e.preventDefault();
      if($(this).val() != ""){

         let action = "infoProducto";
         let producto = $(this).val();
   
         $.ajax(
            {
               url: 'ajax.php',
               type: 'POST',
               // dataType: '',
               async: true,
               data:{action:action, producto:producto},
   
               success: function(response){
                  if(response != "error"){
                     let info = JSON.parse(response);
                     $('#txt_descripcion').html(info.descripcion);
                     $('#txt_existencia').html(info.existencia);
                     $('#txt_cant_producto').val('1');
                     $('#txt_precio').html(info.precio);
                     $('#txt_precio_total').html(info.precio);

                     // Activar cantidad
                     $('#txt_cant_producto').removeAttr('disabled');

                     // Mostrar boton agregar
                     $("#add_product_venta").slideDown();
                  }else{
                     $('#txt_descripcion').html('-');
                     $('#txt_existencia').html('-');
                     $('#txt_cant_producto').val('0');
                     $('#txt_precio').html('-0,00');
                     $('#txt_precio_total').html('-0,00');

                      // Desactivar cantidad
                      $('#txt_cant_producto').attr('disabled', 'disabled');

                       // Ocultar boton agregar
                     $("#add_product_venta").slideUp();
                  }
             
               },
               error: function(error){
                  console.log(error);
               }
            });
      }else{
         $('#txt_descripcion').html('-');
         $('#txt_existencia').html('-');
         $('#txt_cant_producto').val('0');
         $('#txt_precio').html('-0,00');
         $('#txt_precio_total').html('-0,00');

          // Desactivar cantidad
          $('#txt_cant_producto').attr('disabled', 'disabled');

           // Ocultar boton agregar
         $("#add_product_venta").slideUp();
      }
    
    });

   // Validar cantidad el producto antes de agregarlo
   $("#txt_cant_producto").keyup(function(e){
      e.preventDefault();
      let precio_total = $(this).val() * $("#txt_precio").html();
      $("#txt_precio_total").html(precio_total);
      if($(this).val() < 1 || isNaN($(this).val()) || $(this).val() > parseFloat($('#txt_existencia').html()) || ($(this).val()) %1 != 0){
         $("#add_product_venta").slideUp();
      }  else{
         $("#add_product_venta").slideDown();
      }
   });

   //Agregar producto al detalle
   $("#add_product_venta").click(function(e){
      e.preventDefault();
   
      if(parseFloat($("#txt_cant_producto").val()) > 0){
        
         let codproducto = $('#txt_cod_producto').val();
         let cantidad = $('#txt_cant_producto').val();
         let action = 'addProductDetalle';

         $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action,codproducto:codproducto,cantidad:cantidad},

            success: function(response){
            //  console.log(response);    
            if(response != "error campos vacios" && response != "Hubo un error al procesar campos"){
               let info = JSON.parse(response);
               // console.dir(info);
               $("#detalle_venta").html(info.detalle);
               $("#detalle_totales").html(info.totales);

               $('#txt_cod_producto').val('');
               $('#txt_descripcion').html('-');
               $('#txt_existencia').html('-');
               $('#txt_cant_producto').val('0');
               $('#txt_precio').html('-0,00');
               $('#txt_precio_total').html('-0,00');
      
               $('#txt_cant_producto').attr('disabled', 'disabled');

               // Ocultar boton agregar
               $("#add_product_venta").slideUp();
            }
            viewProcesar();         
            },
            error: function(error){
               console.log(error);
            }
         });
      }
   });

   // Anular venta
   $("#btn_anular_venta").click(function(e){
      e.preventDefault();

      let rows = $("#detalle_venta tr").length;
      if(rows > 0){
         let action = 'anularVenta';
         $.ajax(
            {
               url: 'ajax.php',
               type: 'POST',
               // dataType: '',
               async: true,
               data:{action:action},
   
               success: function(response){
               //  console.log(response);  
                if(response != "error"){
                  location.reload();  
                }
                      
               },
               error: function(error){
                  console.log(error);
               }
            });
      }
   });

   // Evento Procesar/Facturar Venta
   $("#btn_facturar_venta").click(function(e){
      e.preventDefault();

      let rows = $("#detalle_venta tr").length;
      if(rows > 0){
         let action = 'procesarVenta';
         let codCliente = $("#idcliente").val();

         $.ajax(
            {
               url: 'ajax.php',
               type: 'POST',
               // dataType: '',
               async: true,
               data:{action:action,codCliente:codCliente},
   
               success: function(response){
                if(response != "Hubo un error al procesar campos" && response != "No hay productos en el carrito"){
                
                  let info = JSON.parse(response);
                  generarPDF(info.codcliente,info.nofactura);
                  location.reload();  
                }else{
                  console.log("no data");
                }
                      
               },
               error: function(error){
                  console.log(error);
               }
            });
      }
   });

   // Modal Form Anular venta
   //Elemento obtenido del archivo lista_venta.php
   $(".anular_factura").click(function(event){
      // El metodo event.preventDefault(); evita que mi pagina se recargue
      event.preventDefault();

      let nofactura = $(this).attr("fac");
      let action = 'infoFactura';
      // console.log(typeof producto);

      $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action, nofactura:nofactura},

            success: function(response){
               // console.log(response);
               if(response != "error"){
                  let info = JSON.parse(response);
                  // console.log(info);

                  $('.bodyModal').html('<form action="" method="post" name="form_form_factura" id="form_form_factura" onsubmit="event.preventDefault(); anularFactura();">'+
                  '<h1><i class="fa-solid fa-box-open" style="font-size:45pt;"></i> <br> Anular Factura</h1>'+
                           
                  '<p>¿Realmente deseas anular esta factura:?</p>'+
                  '<p><strong>No. '+info.nofactura+'</strong></p>'+
                  '<p><strong>Monto: '+info.totalfactura+'</strong></p>'+
                  '<p><strong>Fecha. '+info.fecha+'</strong></p>'+
                  
                  '<input type="hidden" name="action" value="anularFactura">'+
                  '<input type="hidden" name="no_factura" id="no_factura" value="'+ info.nofactura +'" required>'+

                  '<div class="alert alertAddProduct"></div>'+
                  '<div class="botonesModal">'+
                      '<button type="submit" class="btn_new"><i class="fa-solid fa-cart-plus"></i> Anular</button>'+
                      '<a href="#" class="btn_cancel closeModal" onclick="closeModal();"><i class="fa-solid fa-circle-xmark"></i> Cancelar</a>'+
                  '</div>'+
              '</form>');
               }
            },
            error: function(error){
               console.log(error);
            }
         });

      $(".modal").fadeIn();
   });

   // Cambiar Password
   $('.newPass').keyup(function(e){
      e.preventDefault();
      // console.log($(this).val());
      validatePass();
   });

   // Form cambiar contraseña

   $("#frmChangePass").submit(function(e){
      e.preventDefault();
      let passActual = $("#txtPassUser").val();
      let passNuevo = $("#txtNewPassUser").val();
      let confirmPassNuevo= $("#txtPassConfirm").val();
      let action = "changePassword";
      
      // console.log(passActual);
      // console.log(passNuevo);
      // console.log(confirmPassNuevo);

      if(passNuevo != confirmPassNuevo){
         $('.alertChangePass').html('<p style="color:red;">Las contraseñas no son iguales</p>');
         $('.alertChangePass').slideDown();
         return false;
      }
   
      if(passNuevo.length < 5){
         $('.alertChangePass').html('<p style="color:red";>La contraseña debe ser de 5 caracteres como mínimo.</p>');
         $('.alertChangePass').slideDown();
         return false;
      }

      $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action, passActual:passActual, passNuevo:passNuevo},

            success: function(response){
                  if(response != "error"){
                     let info = JSON.parse(response);
                     if(info.cod === '00'){
                        $('.alertChangePass').html('<p style="color:green;">'+info.msg+'</p>');
                        $('#frmChangePass')[0].reset();
                     }else {
                        $('.alertChangePass').html('<p style="color:red;">'+info.msg+'</p>');
                     }
                     $('.alertChangePass').slideDown();
                  }
                  // console.log(response);
            },
            error: function(error){
               console.log(error);
            }
      });

   });

   // Form actualizar datos de la empresa
   $('#frmEmpresa').submit(function(event){
      event.preventDefault();
      
      let intNit = $('#txtNit').val();
      let strNomEmpresa = $('#txtNombre').val();
      let strRazonSocial =  $('#txtRSocial').val();
      let intTelEmpresa =  $('#txtTelEmpresa').val();
      let strEmailEmpresa =  $('#txtEmailEmpresa').val();
      let strDirEmpresa =  $('#txtDirEmpresa').val();
      let intIva =  $('#txtIva').val();
      let action = "updateDataEmpresa";

      if(intNit =='' || strNomEmpresa =='' || strRazonSocial =='' || intTelEmpresa =='' || strEmailEmpresa =='' || strDirEmpresa =='' || intIva ==''){
         $(".alertFormEmpresa").html('<p style="color:red;">Todos los campos son obligatorios</p>');
         $(".alertFormEmpresa").slideDown();
         return false;
      }

      
      $.ajax(
         {
            url: 'ajax.php',
            type: 'POST',
            // dataType: '',
            async: true,
            data:{action:action, data:$('#frmEmpresa').serialize()},

            // beforeSend: function(){
            //    $(".alertFormEmpresa").slideUp();
            //    $(".alertFormEmpresa").html("");
            //    $(".alertFormEmpresa input").attr("disabled", "disabled");
          
            // },

            success: function(response){
               
                     let info = JSON.parse(response);
            
                     if(info["cod"] == '00'){
                      
                        $('.alertFormEmpresa').html('<p style="color:green;">'+info["msg"]+'</p>');
                        $(".alertFormEmpresa input").removeAttr("disabled");
                        $(".alertFormEmpresa input").slideDown();
              
                     }else {
               
                        $('.alertFormEmpresa').html('<p style="color:red";">'+info["msg"]+'</p>');
                     }
                 
                     $('.alertFormEmpresa').slideDown();
               
                  // console.log(response);
            },
            error: function(error){
               console.log(error);
            
            }
      });
   });

});// Fin del ready

function validatePass(){
   let passNuevo = $('#txtNewPassUser').val();
   let confirmPassNuevo = $('#txtPassConfirm').val();
   if(passNuevo != confirmPassNuevo){
      $('.alertChangePass').html('<p style="color:red;">Las contraseñas no son iguales</p>');
      $('.alertChangePass').slideDown();
      return false;
   }

   if(passNuevo.length < 5){
      $('.alertChangePass').html('<p style="color:red";>La contraseña debe ser de 6 caracteres como mínimo.</p>');
      $('.alertChangePass').slideDown();
      return false;
   }

   $('.alertChangePass').html('');
   $('.alertChangePass').slideUp();
}

function anularFactura(){
   let noFactura = $("#no_factura").val();
   let action ='anularFactura';

   $.ajax(
      {
         url: 'ajax.php',
         type: 'POST',
         // dataType: '',
         async: true,
         data:{action:action,noFactura:noFactura},

         success: function(response){
         //  console.log(response);
         if(response == "error"){
            $(".alertAddProduct").html('<p style="color:red;">Error al anular la factura.</p>');
       
         }else{
            let info = JSON.parse(response);
            $(".alertAddProduct").html('<p style="color:green;">Factura anulada con exito.</p>');
            $("#form_form_factura .btn_new").remove();
            $("#form_form_factura .btn_cancel").html("Cerrar");

            $("#row_"+info.nofactura+" p.estado").html('Anulada');
            $("#row_"+info.nofactura+" p.estado").attr('style','background:rgb(249, 4, 64); padding:5px; border-radius:10px;');
   
            // Eliminamos la accion click que tenia antes establecida
            $("#row_"+info.nofactura+" .link_delete").off("click");
            // Eliminamos atributos y clases que antes fueron pre-definidas
            $("#row_"+info.nofactura+" .link_delete").removeClass("anular_factura");
            $("#row_"+info.nofactura+" .link_delete").removeAttr("href");
            $("#row_"+info.nofactura+" .link_delete").attr("href","#");
            $("#row_"+info.nofactura+" .link_delete").attr("style","color:gray; background:rgb(184, 170, 173); border-radius:10px;");  
         }
         },
         error: function(error){
            console.log(error);
         }
      });
}

function generarPDF(cliente, factura){
   let ancho = 1000;
   let alto = 800;
   
   // Calcular X e Y para centrar la ventana

   let x = parseInt((window.screen.width/2) - (ancho / 2));
   let y = parseInt((window.screen.height/2) - (alto / 2));

   $url = '../../../TiendaVirtual/sistema/factura/generaFactura.php?cl='+cliente+'&f='+factura;
   window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}

function del_product_detalle(correlativo){
   let action = 'delProductoDetalle'
   let id_detalle = correlativo;

   $.ajax(
      {
         url: 'ajax.php',
         type: 'POST',
         // dataType: '',
         async: true,
         data:{action:action,id_detalle:id_detalle},

         success: function(response){
            // console.log(response);  
            if(response != "error campos vacios" && response != "Hubo un error al procesar campos"){
             
               let info = JSON.parse(response);
               // console.dir(info);
               $("#detalle_venta").html(info.detalle);
               $("#detalle_totales").html(info.totales);

               $('#txt_cod_producto').val('');
               $('#txt_descripcion').html('-');
               $('#txt_existencia').html('-');
               $('#txt_cant_producto').val('0');
               $('#txt_precio').html('-0,00');
               $('#txt_precio_total').html('-0,00');
      
               $('#txt_cant_producto').attr('disabled', 'disabled');

               // Ocultar boton agregar
               $("#add_product_venta").slideUp();
            } else{
               $("#detalle_venta").html('');
               $("#detalle_totales").html('');
            }    
            viewProcesar();         
         },
         error: function(error){
            console.log(error);
         }
      });
}

// Mostrar / Ocultar boton procesar

function viewProcesar(){
   if($("#detalle_venta tr").length > 0){
      $("#btn_facturar_venta").show();
   }else{
      $("#btn_facturar_venta").hide();
   }
}

// Buscar registros de venta cuando el usuario ingresa a nueva_venta.php
function searchForDetalle(id){
   let action = 'searchForDetalle'
   let user = id;

   $.ajax(
      {
         url: 'ajax.php',
         type: 'POST',
         // dataType: '',
         async: true,
         data:{action:action,user:user},

         success: function(response){
         //  console.log(response); 
         if(response != "error campos vacios" && response != "Hubo un error al procesar campos"){
            // console.log(response);
        
            let info = JSON.parse(response);
            // console.dir(info);
            $("#detalle_venta").html(info.detalle);
            $("#detalle_totales").html(info.totales);

            $('#txt_cod_producto').val('');
            $('#txt_descripcion').html('-');
            $('#txt_existencia').html('-');
            $('#txt_cant_producto').val('0');
            $('#txt_precio').html('-0,00');
            $('#txt_precio_total').html('-0,00');
   
            $('#txt_cant_producto').attr('disabled', 'disabled');

            // Ocultar boton agregar
            $("#add_product_venta").slideUp();
         }    
         viewProcesar();   
         },
         error: function(error){
            console.log(error);
         }
      });
}

function getUrl(){
   let loc = window.location;
   let pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
   return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

// Funcion asociada al boton agregar que esta dentro del modal que esta en el archivo header.php
function sendDataProduct(){
   $('.alertAddProduct').html("");
   let action = 'agregarProducto';
   $.ajax(
      {
         url: 'ajax.php',
         type: 'POST',
         // dataType: '',
         async: true,
         data:{formulario:$("#form_add_product").serialize(), action:action},

         /*
         ¿Cómo funciona la funcion serialize()?

            Recoge los datos del formulario: $("#form_add_product") selecciona el formulario con el ID form_add_product.
            Convierte los datos a una cadena: Al llamar a .serialize(), se obtiene una cadena en el formato clave=valor, donde cada par clave-valor representa un campo del formulario.
         */

         success: function(response){
            // console.log(response);
            if(response === "error"){
               $(".alertAddProduct").html('<p style="color:red;">Errol al agregar el producto</p>')
            }else{
               let info = JSON.parse(response);
               
               //Debemos dejar el espacio entre  .row'+info.producto_id+ y .celPrecio' ya que estamos accediendo a ese elemento especifico como si fuera en CSS
               $('.row'+info.producto_id +' .celPrecio').html(info.nuevo_precio); 
               $('.row'+info.producto_id +' .celExistencia').html(info.nueva_existencia);
               $("#txtCantidad").val(''); 
               $("#txtPrecio").val(''); 
               $(".alertAddProduct").html('<p>Producto guardado correctamente</p>')
            }
         },
         error: function(error){
            console.log(error);
         }
      });
}

// Eliminar Producto
// Funcion asociada al boton eliminar que esta dentro del modal que esta en el archivo header.php
function delProduct(){
   let pr = $("#producto_id").val();

   $('.alertAddProduct').html("");
   let action = 'eliminarProducto';
   $.ajax(
      {
         url: 'ajax.php',
         type: 'POST',
         // dataType: '',
         async: true,
         data:{formulario:$("#form_del_product").serialize(), action:action},

         /*
         ¿Cómo funciona la funcion serialize()?

            Recoge los datos del formulario: $("#form_del_product") selecciona el formulario con el ID form_add_product.
            Convierte los datos a una cadena: Al llamar a .serialize(), se obtiene una cadena en el formato clave=valor, donde cada par clave-valor representa un campo del formulario.
         */

         success: function(response){
            console.log(response);
            if(response === "error"){
               $(".alertAddProduct").html('<p style="color:red;">Errol al eliminar el producto</p>')
            }else{
             
               $('.row'+pr).remove(); 
               $('#form_del_product .btn_new').remove();
            
               $(".alertAddProduct").html('<p>Producto eliminado correctamente</p>')
               $(".closeModal").html("Cerrar");
            }
         },
         error: function(error){
            console.log(error);
         }
      });
}

function closeModal(){
   $("#txtCantidad").val("");
   $("#txtPrecio").val("");
   $('.alertAddProduct').html("");
   $(".modal").fadeOut();
}


