import $ from 'jquery';
import _ from 'lodash';
import 'bootstrap-datepicker';

$.fn.datepicker.dates['es'] = {
  days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
  daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
  daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
  months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
  monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
  today: "Hoy",
  monthsTitle: "Meses",
  clear: "Borrar",
  weekStart: 1,
  format: "dd/mm/yyyy"
};

let options = {
  language: 'es',
  autoclose: true
};

$('[name=expiration_date]').datepicker(options);

$(() => {
  $('#formulario-solicitud-transportista').submit(function () {
    $(this).find('[type=submit]').remove();
    return true;
  });
});


window.$ = $;
window._ = _;