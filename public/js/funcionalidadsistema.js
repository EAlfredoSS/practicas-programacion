//configuracion para notificaciones
Messenger.options = {
  extraClasses: 'messenger-fixed messenger-on-bottom messenger-on-right',
  theme: 'block'
}
//configuracion para cuadros de confirmacion
bootbox.setDefaults({
  locale: "es",
});

//configuracion de notificaciones por defecto para toda la aplicacion
function message(msg,type,time){
  var msg_mod = '';
  switch (type) {
    case 'warning':
      msg_mod = "<div style='float:left;font-size:18px;margin-right:20px'><i class='fa fa-exclamation-circle fa-2x'></i></div>"
      break;
    case 'danger':
      msg_mod = "<div style='float:left;font-size:18px;margin-right:20px'><i class='fa fa-times-circle fa-2x'></i></div>"
      break;
    case 'info':
      msg_mod = "<div style='float:left;font-size:18px;margin-right:20px'><i class='fa fa-info-circle fa-2x'></i></div>"
      break;
    case 'success':
      msg_mod = "<div style='float:left;font-size:18px;margin-right:20px'><i class='fa fa-check-circle fa-2x'></i></div>"
      break;
    default:
  }
  msg_mod = msg_mod + msg;
  Messenger().post({
    message: msg_mod,
    hideAfter: time,
    type: type,
    showCloseButton: true
  });
}
// activa y desactiva elementos dentro de una seccion
function disableElements(el) {
  for (var i = 0; i < el.length; i++) {
    el[i].disabled = true;

    disableElements(el[i].children);
  }
}

function enableElements(el) {
  for (var i = 0; i < el.length; i++) {
    el[i].disabled = false;

    enableElements(el[i].children);
  }
}
//

// Retorna el indice mas alto de una columna seleccionada - datatables
function maxIntValue(table, colSelector) {
  var valArray = table.column(colSelector)
  .data()
  .sort()
  .reverse();
  for (var i=0;i<valArray.length;i++) {
    if (!isNaN(valArray[i])) {
      return parseInt(valArray[i]);
    }
  }
  return 0;
}

//busca si ya esta repetido el valor en la tabla al ingresarlo
function exist_1(table,value,col) {
  var flag = false;
  var tabla = $(''+table+'').DataTable();
  $(''+table+' tr').each(function(row, tr){
    if (typeof tabla.row(tr).data()!='undefined'){
      if(value === tabla.row(tr).data()[col]){
        flag = true;
        return false;
      }
    }
  });
  if(flag){
    return true;
  } else {
    return false;
  }
}

//valida 2 campos a la vez y trata columnas ocultas - datatables
function exist_2(table,value1,value2,col1,col2) {
  var flag = false;
  var tabla = $(''+table+'').DataTable();
  $(''+table+' tr').each(function(row, tr){
    if (typeof tabla.row(tr).data()!='undefined'){
      if(value1 === tabla.row(tr).data()[col1] && value2 === tabla.row(tr).data()[col2]){
        flag = true;
        return false;
      }
    }
  });
  if(flag){
    return true;
  } else {
    return false;
  }
}

//valida 3 campos a la vez y trata columnas ocultas - datatables
function exist_3(table,value1,value2,value3,col1,col2,col3) {
  var flag = false;
  var tabla = $(''+table+'').DataTable();
  // tabla.data()
  //   .each( function ( value, index ) {
  //       console.log( 'Data in index: '+index+' is: '+value[2] );
  //   } );
  $(''+table+' tr').each(function(row, tr){
    if (typeof tabla.row(tr).data()!='undefined'){
      if(value1 === tabla.row(tr).data()[col1] && value2 === tabla.row(tr).data()[col2] && value3 === tabla.row(tr).data()[col3]){
        flag = true;
        return false;
      }
    }
  });
  if(flag){
    return true;
  } else {
    return false;
  }
}

//borra tablas temporales enviandoles el elemento de la vista
function delete_row(evt,table){
  var row = evt.closest('tr');
  var table = evt.closest(table).dataTable();
  bootbox.confirm("¿Esta seguro de querer eliminar este registro?", function(result) {
    if (result){
      table.fnDeleteRow(row);
      message('El registro fue eliminado correctamente','success',5);
    }
  });
}

//retorna fecha actual - formato yyyy-mm-dd
function fecha_sistema(){
  var d = new Date();
  var month = d.getMonth()+1;
  var day = d.getDate();
  var output = d.getFullYear() + '-' +
  (month<10 ? '0' : '') + month + '-' +
  (day<10 ? '0' : '') + day;
  return output;
}

function hora_sistema(){
  var today=new Date();
  var hora=today.getHours();
  var minuto=today.getMinutes();
  var segundo=today.getSeconds();
  var output = hora + ':' + minuto + ':' + segundo;
  return output;
}

function fecha_hora(){
  var output = fecha_sistema()+" "+hora_sistema();
  return output;
}
//inicializar en la interfaz que contenga el select para enfermedades
// $('.selectpicker').selectpicker().filter('.enfermedad').ajaxSelectPicker(enfermedad);
// $('select').trigger('change');

//validacion_general.js
// funcion para evitar que se seleccione el texto de mi web
// IE Evitar seleccion de texto
document.onselectstart=function(){
  if (event.srcElement.type != "text" && event.srcElement.type != "textarea" && event.srcElement.type != "password")
  return false
  else
  return true;
};
// FIREFOX Evitar seleccion de texto
if (window.sidebar){
  document.onmousedown=function(e){
    var obj=e.target;
    if (obj.tagName.toUpperCase() == "SELECT" || obj.tagName.toUpperCase() == "INPUT" || obj.tagName.toUpperCase() == "TEXTAREA" || obj.tagName.toUpperCase() == "PASSWORD")
    return true;
    /*else if (obj.tagName=="BUTTON"){
    return true;
  }*/
  else
  return false;
}
}
// End -->

//funcion para validar qeu se ingresen solo numeros
function soloNumeros(e){
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = "1234567890";
  especiales = [8,39,46,47];
  tecla_especial = false
  for(var i in especiales){
    if(key == especiales[i]){
      tecla_especial = true;
      break;
    }
  }
  if(letras.indexOf(tecla)==-1 && !tecla_especial){
    return false;
  }
}

//a mayuscula
function aMayusculas(obj,id){
  obj = obj.toUpperCase();
  document.getElementById(id).value = obj;
}

function toUpper(control) {
  if (/[a-z]/.test(control.value)) {
    control.value = control.value.toUpperCase();
  }
}

//funcion para validar que se ingresen solo letras
function soloLetras(e){
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = "áéíóúabcdefghijklmnñopqrstuvwxyz";
  especiales = [8,39,46,32,58];
  tecla_especial = false
  for(var i in especiales){
    if(key == especiales[i]){
      tecla_especial = true;
      break;
    }
  }
  if(letras.indexOf(tecla)==-1 && !tecla_especial){
    return false;
  }
}

//funcion para validar que se ingresen solo letras o numeros
function soloNumerosLetras(e){
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = "áéíóúabcdefghijklmnñopqrstuvwxyz0123456789";
  especiales = [8,39,46,32,13,58,47];
  tecla_especial = false
  for(var i in especiales){
    if(key == especiales[i]){
      tecla_especial = true;
      break;
    }
  }
  if(letras.indexOf(tecla)==-1 && !tecla_especial){
    return false;
  }
}

// funcion para validar si es una cedula correcta
function iscedula(textcedula) {
  var aviso1 = 0, aviso2 = 0, contador1 = 0, contador2 = 0;
  var cedula_valida = false;
  var cedula = textcedula.value;//CAPTURA LA CEDULA EN LA VARIABLE
  digitos = cedula.split("");	//DIVIDO LA CEDULA EN DIGITOS
  totdigitos = digitos.length;	//NUMERO DE DIGITOS QUE HA INGRESADO

  if (totdigitos < 1) {
    // no se hace nada porque el campo esta vacio
  } else {
    if (totdigitos == 10) {		//QUE SEAN 10 NUMEROS
      total = 0;
      digito = (digitos[9]*1);

      for ( i=0; i < totdigitos; i++ ) {
        if (digitos[i] % 2 == 0) {
          aviso1 = 1;
          contador1 = contador1 + 1;
        }
      }

      for ( i=1; i < totdigitos; i=i+2) {
        if (digitos[i] % 2 == 0){
          aviso2 = 1;
          contador2 = contador2 + 1;
        }
      }

      if ( ( aviso1 == 1 && contador1 == 10 ) || ( aviso2 == 1 && contador2 == 5 ) ) {
        alert('Cédula incorrecta, por favor verifique el ingreso');
        return;
      }

      for ( i=0; i < (totdigitos-1); i++ ){
        mult = 0;
        if ( ( i%2 ) != 0 ) {
          total = total + ( digitos[i] * 1 );
        } else {
          mult = digitos[i] * 2;
          if ( mult > 9 )
          total = total + ( mult - 9 );
          else
          total = total + mult;
        }
      }

      decena = total / 10;
      decena = Math.floor( decena );
      decena = ( decena + 1 ) * 10;
      final = ( decena - total );

      if ((final == 10 && digito == 0) || (final == digito)) {
        //alert('Cédula correcta');
      } else {
        alert('Cédula incorrecta, por favor verifique el ingreso');
        return;
      }
    } else {
      alert('Faltan campos en la cédula');
    }
  }
}

// Desabilitamos el clic derecho del mouse
document.oncontextmenu = function(){return false;}

// // permite boton atras del navegador - jquery load issue - verificar otros modulos
// $(function(){
//   // Bind the event.
//   $(window).hashchange( function(){
//     // Alerts every time the hash changes!
//     // alert( location.hash );
//     var hash = location.hash;
//     hash = hash.replace( /^#/, '' ) || 'blank';
//     if (hash!='blank'){
//       if (hash=='lista_preparados'){
//         $("#page-wrapper").load(BASE_URL+"modulo3/consulta/"+hash);
//       }else{
//         $("#page-wrapper").load(BASE_URL+"modulo3/"+MODULO+"/"+hash);
//       }
//     }
//
//   })
//   // Trigger the event (useful on page load).
//   $(window).hashchange();
// });
