$(function () {
    $('#myTab li:last-child a').tab('show');
})
function marcarTodos() {
    $("#marcarTodos").click(
            function ($) {
                var marcado = $("#marcarTodos").is(":checked");
                if (!marcado)
                    $("#diasHabilitados :checkbox").attr('checked', true);
                else
                    $("#diasHabilitados :checkbox").attr('checked', false);
            });
}
function  tablas() {
    if ($("#accordion").size() > 0) {
        $(document).ready(function () {
            $("#accordion").accordion();
            $('#example').DataTable({
                "pagingType": "full_numbers"
            });
        });
    } else {
        if ($("#example").size() > 0) {
            $(document).ready(function () {
                $('#example').DataTable({
                    "pagingType": "full_numbers",
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "order": [[2, "desc"]],
                    "scrollY": "450px",
                    "scrollCollapse": true
                });
            });
        }
        if ($("#estadoExp").size() > 0) {
            $(document).ready(function () {
                $('#estadoExp').DataTable({
                    "pagingType": "full_numbers",
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "order": [[0, "asc"]],
                    "scrollY": "480px",
                    "scrollCollapse": true
                });
            });

        }
    }
}

function validarCampos() {
    var valid = true;
    var estado = $("#seqPryEstadoProceso").val();
    var required = "";

    if ($('input[id="bolTipoPersonaInterventor"]').is(':checked')) {
        $("#numNitInterventor").attr('class', 'form-control');
        $("#txtNombreRepLegalInterventor").attr('class', 'form-control');
        $("#numTelefonoRepLegalInterventor").attr('class', 'form-control');
        $("#txtDireccionRepLegalInterventor").attr('class', 'form-control');
        $("#txtCorreoRepLegalInterventor").attr('class', 'form-control');

    }
    if ($('input[id="bolTipoPersonaInterventor1"]').is(':checked')) {
        $("#numCedulaInterventor").attr('class', 'form-control');
        $("#numTProfesionalInterventor").attr('class', 'form-control');
    }

    if ($('input[id="seqTipoLicencia2"]').val() == 2) {
        $("#txtResEjecutoria2").attr('class', 'form-control');
    }

    for (var i = 1; i <= estado; i++) {
        if (i == 1) {
            required = "required";

        } else {
            required = "required" + i;
        }

        $.each($("#frmProyectos input." + required), function (index, value) {
            $("#val_" + $(this).attr("id")).css("display", "none");
            $("#" + $(this).attr("id")).css("border", "1px solid #ccc");
            //console.log("value : " + $("#txtLicenciaConstructor").val());
            if (!$(value).val()) {
                //  console.log("paso : " + required + " -> " + $(value).val());
                $("#" + $(this).attr("id")).css("border", "1px solid red");
                $("#val_" + $(this).attr("id")).css("display", "inline");
                // console.log($(this).attr("id") + " input");
                valid = false;
            }

        });

        // console.log($("#frmProyectos select.required"));
        $.each($("#frmProyectos select." + required), function (index, value) {
            $("#val_" + $(this).attr("id")).css("display", "none");
            $("#" + $(this).attr("id")).css("border", "1px solid #ccc");
            if ($(value).val() == 0) {
                $("#" + $(this).attr("id")).css("border", "1px solid red");
                $("#val_" + $(this).attr("id")).css("display", "inline");
                console.log($(this).attr("id") + "select");
                valid = false;
            }
        });

        $.each($("#frmProyectos input[type=email]." + required), function (index, value) {
            $("#val_" + $(this).attr("id")).css("display", "none");
            $("#" + $(this).attr("id")).css("border", "1px solid #ccc");
            var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
            //console.log(caract.test($(value).val()));
            if (caract.test($(value).val()) == false) {
                $("#val_" + $(this).attr("id")).css("display", "inline");
                $("#val_" + $(this).attr("id")).html("Correo erroneo! ");
                // console.log($(this).attr("id") + " input email");
                valid = false;
            }
        });

        $.each($("#frmProyectos textArea." + required), function (index, value) {
            $("#val_" + $(this).attr("id")).css("display", "none");
            $("#" + $(this).attr("id")).css("border", "1px solid #ccc");
            if ($(value).val() == 0) {
                // console.log("paso3");
                $("#" + $(this).attr("id")).css("border", "1px solid red");
                $("#val_" + $(this).attr("id")).css("display", "inline");
                // console.log($(this).attr("id"));
                valid = false;
            }
        });
        console.log("paso" + i);
    }

    $.each($("#frmProyectos input"), function (index, value) {
        if ($(value).val() != 0 && $(value).val() != null) {
            //console.log("value : " + $(value).val() + " index -> " + index);
            var separador = "Tel";
            var num = $(this).attr("id");
            if (num != 'undefined' && num != null && num != "") {
                separador = "Tel";
                num = num.split(separador);
                if (num.length > 1) {
                    $("#val_" + $(this).attr("id")).css("display", "none");
                    $("#" + $(this).attr("id")).css("border", "1px solid #ccc");
                    // console.log("id : " + $(this).attr("id") + " tel: " + $("#" + $(this).attr("id")).val().length);
                    if ($("#" + $(this).attr("id")).val().length != 7 && $("#" + $(this).attr("id")).val().length != 10) {
                        $("#" + $(this).attr("id")).css("border", "1px solid red");
                        $("#val_" + $(this).attr("id")).css("display", "inline");
                        $("#val_" + $(this).attr("id")).html("Debe tener 7 o 10 numeros");
                        valid = false;
                    }
                }
                // console.log("num - > " + num.length + " valid = " + valid);
            }
        }
    });

    return valid;

}
// Autora: Liliana Basto
// Funcion que almacena todos los formularios 
function almacenarIncripcion() {
    var valid = validarCampos();
    if (valid == false) {
        $("#mensajes").html("Por favor verifique todos los campos obligatorios(*) resaltados en rojo");
        $("#mensajes").css("color", "red");
        $("#mensajes").css("padding-top", "10px");
        $("#mensajes").css("font-weight", "bold");
    }
    // console.log("valid : " + valid);
    if (valid) {
        var url = $("#txtArchivo").val();
        $("#mensajes").html("");
        // var url = "./contenidos/proyectos/contenidos/datosOferente.php"; // El script a dónde se realizará la petición.
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frmProyectos").serialize(), // Adjuntar los campos del formulario enviado.
            success: function (data)
            {
                $("#contenido").html(data); // Mostrar la respuestas del script PHP.
                tablas();
            }
        });
    }
}

function adicionarOferente() {
    var intId = $("#buildyourform select").length + 1;
    var fieldWrapper = $("<div class=\"form-group\" id=\"field" + intId + "\" />");
    var fName = "<div id=\"table" + intId + "\"><div class=\"col-md-3\"><label>Oferente(*)</label>";
    var fType = "<select name=\"seqOferente[]\" id=\"seqOferente_" + intId + "\" class=\"form-control required\"   onchange=\"limpiarOferente(" + intId + ");\" style=\"position: relative;float: left; width: 85%\">";
    $("#seqOferente_" + (intId - 1) + " option").each(function () {
        fType += "<option value=" + $(this).attr('value') + ">" + $(this).text() + "</option>";
    });
    fType += "</select></div>";
    fType += "<div class=\"col-md-3\"><label class=\"control-label\" >Nombre Contaco Oferente</label>";
    fType += "<input name=\"txtNombreContactoOferente[]\" type=\"text\" id=\"txtNombreContactoOferente_" + intId + "\" onBlur=\"sinCaracteresEspeciales(this);\" class=\"form-control\" style=\"width:160px;\"/>";
    fType += "</div>";
    fType += "<div class=\"col-md-3\">";
    fType += "<label class=\"control-label\" >Correo Contacto</label> ";
    fType += "<input name=\"txtCorreoOferente[]\" type=\"text\" id=\"txtCorreoOferente\" value=\"\" onBlur=\"sinCaracteresEspeciales(this);\" class=\"form-control\" style=\"width:140px;\"/></div>";
    fType += "<div class=\"col-md-2\"><label class=\"control-label\" >Telefono Oferente</label>";
    fType += "<input name=\"numTelContactoOferente[]\" type=\"text\" id=\"numTelContactoOferente_" + intId + "\" value=\"\" onBlur=\"sinCaracteresEspeciales(this); soloNumeros(this);\" class=\"form-control\" style=\"position: relative; float: left;width:70%;\"/>";
    fType += "<img src=\"recursos/imagenes/remove.png\" width=\"22px\" onclick=\"removerOferente(table" + intId + ")\" style=\"position: relative; float: left; width:20% \"/>";
    fType += "<div id='val_numTelContactoOferente_" + intId + "' class='divError'>Debe diligenciar el numero de contacto del Oferente</div></div></div>";
    fName += fType;
    fName += "</div></div>";
    fieldWrapper.append(fName);
    $("#buildyourform").append(fieldWrapper);
}

function removerOferente(id) {
    $(id).remove();
}

function obtenerModalidadProyecto(value) {

    var parametros = {
        "valor": value
    };
    $.ajax({
        data: parametros,
        url: 'contenidos/proyectos/contenidos/datosSelect.php',
        type: 'post',
        dataType: "json",
        success: function (response) {

            var select = $('#seqPryTipoModalidad');
            $('#seqPryTipoModalidad').empty();
            var options = select.attr('options');
            var selectedOption = '';
            $("#seqPryTipoModalidad").append('<option value="0">Seleccione</option>');
            $.each(response, function (val, text) {

                $("#seqPryTipoModalidad").append('<option value=' + val + '>' + text + '</option>');
            });
        }
    });
}
function showMenu() {
    $(document).ready(function () {
        $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
            //console.log("paso2" + event.preventDefault());
            event.preventDefault();
            event.stopPropagation();
            $(this).parent().siblings().removeClass('open');
            $(this).parent().toggleClass('open');
        });
    });
}

function limpiarOferente(int) {
    $("#txtNombreContactoOferente_" + int).val("");
    $("#txtCorreoOferente_" + int).val("");
    $("#numTelContactoOferente_" + int).val("");
}

function addAmparos() {
    var intId = $("#idAmparos select").length + 1;
    var fieldWrapper = $("<div class=\"form-group\" id=\"amp" + intId + "\" />");
    var fType = " <fieldset style=\"border: 1px dotted #024457; width: 95%;margin-left: 10px; padding: 5px;\"> <legend style=\"text-align: right\"><p><h5>&nbsp;<img src=\"recursos/imagenes/add.png\" width=\"20px\" onclick=\"addAmparos();\"  /><b>&nbsp; Adicionar  Amparo</b>&nbsp; <img src=\"recursos/imagenes/remove.png\" width=\"20px\"  onclick='removerOferente(amp" + intId + ")'/><b style=\"text-align: right\">&nbsp; Eliminar Amaparo</b></h5></p></legend>";
    fType += "<div class=\"col-md-3\"> <input name=\"seqAmparo[]\" type=\"hidden\" id=\"seqAmparo\" value=\"\" onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control\"><label class=\"control-label\" >Tipo de Amparo</label><input name=\"seqAmparoPadre[]\" type=\"hidden\" id=\"seqAmparoPadre\" value='' onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control\"><select id=\"seqTipoAmparo_" + intId + "\" name=\"seqTipoAmparo[]\" class=\"form-control required5\" style=\"width: 75%\">";
    $("#seqTipoAmparo_1" + " option").each(function () {
        fType += "<option value=" + $(this).attr('value') + ">" + $(this).text() + "</option> ";
    });
    fType += "</select></div><div class=\"col-md-2\"><label class=\"control-label\" >Vigencia Desde:<div class='inner-addon left-addon'><i class='glyphicon glyphicon-calendar'></i></label><input name=\"fchVigenciaIni[]\" type=\"text\" id=\"fchVigenciaIni_" + intId + "\" value=\"\" size=\"12\" readonly=\"\"  class=\"form-control required5\"  style=\"width: 80%; position: relative; float: left\"></div></div>";
    fType += "<div class=\"col-md-2\"><label class=\"control-label\" >Vigencia Hasta:</label><div class='inner-addon left-addon'><i class='glyphicon glyphicon-calendar'></i><input name=\"fchVigenciaFin[]\" type=\"text\" id=\"fchVigenciaFin_" + intId + "\" value=\"\" size=\"12\" readonly=\"\"  class=\"form-control required5\"  style=\"width: 80%; position: relative; float: left\"></div></div>";
    fType += "<div class=\"col-md-3\"><label class=\"control-label\" >Valor Asegurado</label><input name=\"valAsegurado[]\" type=\"text\" id=\"valAsegurado_" + intId + "\" value=\"\" onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control required5\"></div> ";
    fType += "<div class=\"col-md-2\"><br><br></div>";
    fType += "<p>&nbsp;</p></fieldset><p>&nbsp;</p></div><div id='divCalendar' style='display:none'></div>";
    fieldWrapper.append(fType);
    $("#idAmparos").append(fieldWrapper);
}

function addProrroga(idPadre, usuario) {
    //$("#demo" + idPadre).addClass("collapse in");
    $("#demo" + idPadre).css({"display": "block"});

    var intIdHijo = $("#demo" + idPadre + " select").length + 1;

    var fieldWrapper = $("<div class=\"form-group\" id=\"ampHijo" + idPadre + "_" + intIdHijo + "\" />");
    var fType = "<div class=\"col-md-3\"><label class=\"control-label\" >Porroga " + intIdHijo + "</label><select id=\"seqTipoAmparo" + idPadre + "_" + intIdHijo + "\" name=\"seqTipoAmparo[]\" class=\"form-control required5\" style=\"width: 75%\">";
    fType += "<option value='6'>Prorroga</option>  <input name=\"seqAmparo[]\" type=\"hidden\" id=\"seqAmparo\" value=\"\" onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control\"><input name=\"seqAmparoPadre[]\" type=\"hidden\" id=\"seqAmparoPadre\" value=\"" + idPadre + "\" onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control required5\">";
    fType += "</select></div><div class=\"col-md-2\"><label class=\"control-label\" >Vigencia Desde:</label><div class='inner-addon left-addon'><i class='glyphicon glyphicon-calendar'></i><input name=\"fchVigenciaIni[]\" type=\"text\" id=\"fchVigenciaIni" + idPadre + "_" + intIdHijo + "\" value=\"\" size=\"12\" readonly=\"\"  class=\"form-control required5\"  style=\"width: 70%; position: relative; float: left\"></div></div>";
    fType += "<div class=\"col-md-2\"><label class=\"control-label\" >Vigencia Hasta:</label><div class='inner-addon left-addon'><i class='glyphicon glyphicon-calendar'></i><input name=\"fchVigenciaFin[]\" type=\"text\" id=\"fchVigenciaFin" + idPadre + "_" + intIdHijo + "\" value=\"\" size=\"12\" readonly=\"\"  class=\"form-control required5\"  style=\"width: 70%; position: relative; float: left\"></div></div>";
    fType += "<div class=\"col-md-2\"><label class=\"control-label\" >Valor Asegurado</label><input name=\"valAsegurado[]\" type=\"text\" id=\"valAsegurado" + idPadre + "_" + intIdHijo + "\" value=\"\" onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control required5\"></div> ";
    fType += " <div class=\"col-md-2\" style=\"width: 5.5%\"><label class=\"control-label\" >Aprobo</label> <br><input name=\"bolAproboAmparo[]\" type=\"checkbox\" id=\"bolAprobo" + idPadre + "_" + intIdHijo + "\"  value=\"1\"  style=\"height: 15px; text-align: center\" onclick=\"selectUsuario(this.id, " + usuario + ")\">&nbsp;&nbsp;<input type=\"hidden\" name=\"seqUsuario[]\" id=\"seqUsuario" + idPadre + "_" + intIdHijo + "\" value=\"'0'\"></div>";
    fType += "<div class=\"col-md-2\"><br><br><input type=\"button\"  value=\"Prorroga\" class=\"btn_deleted\"  onclick='removerOferente(ampHijo" + idPadre + "_" + intIdHijo + ")'/></div>";
    fType += "<p>&nbsp;</p></fieldset><p>&nbsp;</p></div><div id='divCalendar' style='display:none'></div>";
    fieldWrapper.append(fType);
    $("#demo" + idPadre).append(fieldWrapper);
}


var fncActivarMenuNietos = function () {
    $('.dropdown-submenu a.test').on("click", function (e) {
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
    eliminarObjeto("divNietos");
    YAHOO.util.Event.onContentReady(
            "divNietos",
            fncActivarMenuNietos
            );
}

YAHOO.util.Event.onContentReady(
        "divNietos",
        fncActivarMenuNietos
        );

function selectUsuario(valor, user) {
    var id = valor.split("bolAprobo");
    //console.log("paso" + id[1]);
    if ($("#" + valor).is(':checked')) {
        // console.log("paso");
        $("input[id=seqUsuario" + id[1] + "]:hidden").val(user);
    } else {
        $("input[id=seqUsuario" + id[1] + "]:hidden").val('0');

    }
}

function ocultarDivEnt(valor, div) {
    // console.log(valor + " div " + div)
    var id = div.split("Div");
    if (valor == 1) {
        $("#" + div).hide();
    } else {
        $("#" + id[0]).val("");
        $("#" + div).show();
    }
}

function addFideicomitente() {

    var intId = $("#fideicomiso select").length + 1;
    var fieldWrapper = $("<div class=\"form-group\" id=\"fidehijo" + intId + "\" />");
    var fType = "<div class=\"col-md-4\">";
    fType += "<label class=\"control-label\" >Tipo </label>";
    fType += "<select name=\"seqTipoFideicomitente[]\" id=\"seqTipoFideicomitente" + intId + "\" class=\"form-control required5\" style=\"width: 78%\">";
    fType += "<option value=\"\">Ninguna</option>";
    fType += "<option value=\"1\">Fideicomitente</option>";
    fType += "<option value=\"2\">Beneficiario</option>";
    fType += "</select></div>";
    fType += "<div class=\"col-md-4\">";
    fType += "<label class=\"control-label\" >Nombre Entidad o Raz&oacute;n Social</label>";
    fType += "<input type=\"text\" name=\"txtNombreFideicomitente[]\" id=\"txtNombreFideicomitente" + intId + "\" value=\"\" class=\"form-control required5\"> \n\
    <input name=\"seqFideicomitente[]\" type=\"hidden\" id=\"seqFideicomitente" + intId + "\" value='' onblur=\"sinCaracteresEspeciales(this);\"  class=\"form-control\">";
    fType += "<div class=\"col-sm-12\"><div id=\"txtNombreFideicomitenteContenedor" + intId + "\"></div></div></div>";
    fType += "<div class=\"col-md-3\"><br><br><input type=\"button\"  value=\"Eliminar\" class=\"btn_deleted\"  onclick='removerOferente(fidehijo" + intId + ")'/></div> </div></div></div>";
    //  fType += "onclick='removerOferente(fideHijo" + intId + ")'/> </div></div></div>";
    fType += "<div class=\"col-md-3\"><p>&nbsp;</p></div>";
    fieldWrapper.append(fType);
    $("#fideicomiso").append(fieldWrapper);
    // activarAutocompletar('txtNombreFideicomitente', 'txtNombreFideicomitenteContenedor', './contenidos/cruces2/fideicomitentesAdd.php', intId);
}

function activarAutocompletar(txtInput, contenedor, url, cant) {

    for (var i = 1; i <= cant; i++) {
        // console.log('#' + txtInput + '' + i);
        autocompletar(txtInput + '' + i, contenedor + '' + i, url, '');
        $('#' + txtInput + '' + i).removeClass('yui-ac-input');
    }
    // autocompletar( 'txtSubsecretario' , 'txtSubsecretarioContenedor' , './contenidos/cruces2/nombres.php' , '' ); $('#txtSubsecretario').removeClass('yui-ac-input');
    //  autocompletar( txtInput , contenedor , url , '' ); $('#'+txtInput).removeClass('yui-ac-input');
}

function activarEditorTiny(id, cant) {

    tinymce.remove();
    var bolCerrar = 0;

    for (var i = 1; i <= cant; i++) {
        if ($("#bolCerrar").length > 0) {
            if ($("#bolCerrar").is(":checked")) {
                bolCerrar = 1;
            }
        }


        tinymce.init({
            selector: 'textarea#' + id + "" + i,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            readonly: bolCerrar,
            height: 100,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor textcolor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code wordcount'
            ],
            toolbar: 'undo redo | styleselect fontselect fontsizeselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify |  bullist numlist outdent indent'
        });
    }
}

function addSeguimientoFicha() {
    var intId = $("#ficha textarea").length + 1;
    var fieldWrapper = $("<div class=\"form-group\" id=\"intV" + intId + "\" />");
    var fType = "<fieldset style='border: 1px dotted #024457; width: 95%;margin-left: 10px; padding: 5px;'>";
    fType += "<legend style='text-align: right; cursor: hand; width: 23%;'><p>&nbsp;</p><input type='button' value='Adicionar' class='btn_add' onclick='addSeguimientoFicha();'><input type='button'  value='Eliminar' class='btn_deleted'  onclick='removerOferente(intV" + intId + ")'/>";
    fType += "<p>&nbsp;</p></legend>";
    fType += "<div class='col-md-12'> ";
    fType += "<label class='control-label' >Seguimiento</label> ";
    fType += "<input type='hidden' name='seqFichaTexto[]' id='seqFichaTexto' value=''>";
    fType += "<textarea rows='10' cols='200' name='txtFichaTexto[]' id='comentarios" + intId + "'></textarea>";
    fType += "</div> ";
    fType += "<p>&nbsp;</p> </fieldset></div>";
    fieldWrapper.append(fType);
    $("#ficha").append(fieldWrapper);
    activarEditorTiny('comentarios', intId);
    activarAutocompletar('txtNombreInformador', 'txtNombreInformadorContenedor', './contenidos/cruces2/nombres.php', intId);
}
function addObsInterventoria() {
    var intId = $("#ficha textarea").length + 1;
    var fieldWrapper = $("<div class=\"form-group\" id=\"intV" + intId + "\" />");
    var fType = "<fieldset style='border: 1px dotted #024457; width: 95%;margin-left: 10px; padding: 5px;'>";
    fType += "<legend style='text-align: right; cursor: hand; width: 23%;'><p>&nbsp;</p><input type='button' value='Adicionar' class='btn_add' onclick='addObsInterventoria();'><input type='button'  value='Eliminar' class='btn_deleted'  onclick='removerOferente(intV" + intId + ")'/>";
    fType += "<p>&nbsp;</p></legend>";
    fType += "<div class='col-md-12'> ";
    fType += "<label class='control-label' >Conclusiones de Interventoria</label> ";
    fType += "<input type='hidden' name='seqFichaTexto[]' id='seqFichaTexto' value=''>";
    fType += "<textarea rows='10' cols='200' name='txtObservaciones[]' id='comentarios" + intId + "'></textarea>";
    fType += "</div> ";
    fType += "<p>&nbsp;</p> </fieldset></div>";
    fieldWrapper.append(fType);
    $("#ficha").append(fieldWrapper);
    activarEditorTiny('comentarios', intId);

}
var fncEditor = function () {
    activarEditorTiny('comentarios', 1);
    activarAutocompletar('txtNombreInformador', 'txtNombreInformadorContenedor', './contenidos/cruces2/nombres.php', 1);
    eliminarObjeto('segOculto');
    YAHOO.util.Event.onContentReady('segOculto', fncEditor);
}
YAHOO.util.Event.onContentReady('segOculto', fncEditor)

var fncTiny = function () {
    activarEditorTiny('comentarios', $("#segOcultoSeg").html());
    eliminarObjeto('segOcultoSeg');
    YAHOO.util.Event.onContentReady('segOcultoSeg', fncTiny);

}

YAHOO.util.Event.onContentReady('segOcultoSeg', fncTiny);

function dataForm_Archivos(formulario) {
    // console.log("formulario -> " + formulario);
    var nuevoFormulario = new FormData();
    $(formulario).find(':input').each(function () {
        var elemento = this;
        //Si recibe tipo archivo 'file'
        if (elemento.type === 'file') {
            if (elemento.value !== '') {
                var file_data = $('input[type="file"]')[0].files;
                for (var i = 0; i < file_data.length; i++) {
                    nuevoFormulario.append(elemento.name, file_data[i]);
                }
            }
        }
    });

    return nuevoFormulario;
}

function obtenerPlantillaUnidades(tipo) {

    var valid = true;
    // console.log("proyecto " + $('#customFile').val());
    if (($('select[name=seqProyecto]').val() == "" || $('select[name=seqProyecto]').val() == null) && tipo == 1) {
        $("#mensajes").html("<div class='alert alert-danger'><h5>Alerta!!! seleccione un proyecto </h5></div>");
        $("#seqProyecto").css("border", "1px solid #ccc");
        $("#val_seqProyecto").css("display", "inline");
        valid = false;
    }
    if (tipo == 2 || tipo == 3) {
        if (($('select[name=seqProyectoPadre]').val() == "" || $('select[name=seqProyectoPadre]').val() == null)) {
            $("#mensajes").html("<div class='alert alert-danger'><h5>Alerta!!! seleccione un proyecto </h5></div>");
            $("#seqProyectoPadre").css("border", "1px solid #ccc");
            $("#val_seqProyectoPadre").css("display", "inline");
            valid = false;
        }
    }

    if (($('#customFile').val() == "" || $('#customFile').val() == null) && tipo == 3) {
        $("#mensajes").html("<div class='alert alert-danger'><h5>Alerta!!! seleccione un archivo </h5></div>");
        $("#customFile").css("border", "1px solid #ccc");
        $("#val_customFile").css("display", "inline");
        valid = false;
    }
    console.log("valid **  " + valid);
    if (valid) {
        if (tipo == 1) {
            location.href = './contenidos/proyectos/plantillas/plantillaUnidades.php?seqProyecto=' + $('select[name=seqProyecto]').val();
        }
        else if (tipo == 3) {
            return valid;
        } else {
            location.href = './contenidos/proyectos/plantillas/plantillaEstadoUnidades.php?seqProyecto=' + $('select[name=seqProyectoPadre]').val();
        }

    }

}

function addComite() {
    var intId = $("#actasComite select").length + 1;
    var fieldWrapper = $("<div id=\"subComite" + intId + "\" />");
    var fType = "<fieldset style='border: 1px dotted #024457; width: 95%;margin-left: 10px; padding: 5px;'>";
    fType += "<legend style='text-align: right; cursor: hand'><p><h5>&nbsp;<img src='recursos/imagenes/add.png' width='20px' onclick='addComite();'  /><b style='' onclick='addComite();'>&nbsp; Adicionar Actas</b>&nbsp;\n\
               <img src='recursos/imagenes/remove.png' width='20px' onclick='removerOferente(subComite" + intId + ")'><b style='text-align: right' onclick='removerOferente(subComite" + intId + ")'>&nbsp; Eliminar Acta</b>";
    fType += "</h5></p></legend>";
    fType += "<div class='col-md-4'>";
    fType += "<label class='control-label' >Número de Acta</label><br>";
    fType += "<input type='number' name='numActaComite[]' id='numActaComite" + intId + "' value='' class='form-control required4'>";
    fType += "<input type='hidden' name='seqProyectoComite[]' id='seqProyectoComite" + intId + "' value=''>";
    fType += "<div id='val_numActaComite" + intId + "' class='divError'>Diligenciar Campo</div></div>";
    fType += "<div class='col-md-3'> ";
    fType += "<label class='control-label' >Fecha Acta</label> <div class='inner-addon left-addon'><i class='glyphicon glyphicon-calendar'></i>";
    fType += "<input name='fchActaComite[]' type='text' id='fchActaComite" + intId + "' value='' size='12' readonly=''  class='form-control required4'  style='width: 70%; position: relative; float: left'></div>";
    fType += "<div id='val_fchActaComite" + intId + "' class='divError'>Diligenciar Campo</div></div>";
    fType += "<div class='col-md-4' style='text-align: right'>";
    fType += "<div class='btn-group btn-group-toggle' data-toggle='buttons'>";
    fType += "<label class='btn btn-secondary active alert-success' onclick='document.getElementById(\"bolAproboProyecto" + intId + "\").value =1;'>";
    fType += "<input type='radio'  id='bolAproboProyectoAp" + intId + "' value='1' autocomplete='off' > Aprobado";
    fType += "</label>";
    fType += "<label class='btn btn-secondary alert-danger' onclick='document.getElementById(\"bolAproboProyecto" + intId + "\").value =0;'>";
    fType += "<input type='radio'  id='bolAproboProyectoNoap" + intId + "' value='0'   autocomplete='off'> No Aprobado";
    fType += "</label>";
    fType += "<input type='hidden' name='bolAproboProyecto[]' id='bolAproboProyecto" + intId + "' value=''>";
    fType += "</div></div>";
    fType += "<div class='col-md-4'> ";
    fType += "<label class='control-label' >Número de resoluci&oacute;n</label><br>   ";
    fType += "<input type='number' name='numResolucionComite[]' id='numResolucionComite' value='' class='form-control required4'>";
    fType += "<div id='val_numResolucionComite" + intId + "' class='divError'>Diligenciar Campo</div></div>";
    fType += "<div class='col-md-3'> ";
    fType += "<label class='control-label' >Fecha Resoluci&oacute;n</label> ";
    fType += "<div class='inner-addon left-addon'><i class='glyphicon glyphicon-calendar'></i><input name='fchResolucionComite[]' type='text' id='fchResolucionComite" + intId + "' value='' size='12' readonly=''  class='form-control required4'  style='width: 70%; position: relative; float: left'></div>";
    fType += "<div id='val_fchResolucionComite" + intId + "' class='divError'>Diligenciar Campo</div></div> ";
    fType += "<div class='col-md-4'> ";
    fType += "<label class='control-label' >Entidad</label> ";
    fType += "<select name='seqEntidadComite[]' id='seqEntidadComite" + intId + "' class='form-control required4'>";
    fType += "	<option value=''>Seleccione</option>";
    $("#seqEntidadComite1 option").each(function () {
        fType += "<option value=" + $(this).attr('value') + ">" + $(this).text() + "</option>";
    });
    fType += "</select>";
    fType += "<div id='val_seqEntidadComite" + intId + "' class='divError'>Diligenciar Campo</div></div> ";
    fType += "<div class='col-md-12'> ";
    fType += "<label class='control-label' >Observaciones Acta</label>";
    fType += "<textarea name='txtObservacionesComite[]' id='txtObservacionesComite" + intId + "' class='form-control required4'></textarea>";
    fType += "<div id='val_txtObservacionesComite" + intId + "' class='divError'>Diligenciar Campo</div></div>";
    fType += "<div class='col-md-4'>";
    fType += "<label class='control-label' >Comite Aprobado Condicionado?</label>";
    fType += "<div class='btn-group btn-group-toggle' data-toggle='buttons'>";
    fType += "<label class='btn btn-secondary active alert-success' style='margin: 0' onclick='ocultarDivEnt(0, \"txtCondicionesComite" + intId + "Div\");document.getElementById(\"bolCondicionesComite" + intId + "\").value =1;'>";
    fType += "<input type='radio'  id='bolCondiciones" + intId + "'  autocomplete='off' value='1' > SI";
    fType += "</label>";
    fType += "<label class='btn btn-secondary alert-danger' style='margin: 0' onclick='ocultarDivEnt(1, \"txtCondicionesComite" + intId + "Div\");document.getElementById(\"bolCondicionesComite" + intId + "\").value =0;'>";
    fType += "<input type='radio'  id='bolCondiciones" + intId + "' value='0' autocomplete='off'  > NO </label>  ";
    fType += "<input type='hidden' name='bolCondicionesComite[]' id='bolCondicionesComite" + intId + "' value='0'>";
    fType += "</div></div>";
    fType += "<div class='col-md-8' id='txtCondicionesComite" + intId + "Div'> ";
    fType += "<label class='control-label' >Condiciones</label>  ";
    fType += "<textarea name='txtCondicionesComite[]' id='txtCondicionesComite" + intId + "' class='form-control'></textarea>";
    fType += "</div>";
    fType += "<p>&nbsp;</p></fieldset>";
    fType += "</div><div id='divCalendar' style='display:none'></div>";
    fieldWrapper.append(fType);
    $("#actasComite").append(fieldWrapper);
}

function moverImagen(ruta, tipo, nombre, idProyecto) {
    var url = "./contenidos/administracionProyectos/moverImagenes.php"; // El script a dónde se realizará la petición.
    var parametros = {
        "ruta": ruta,
        "nombre": nombre,
        "tipo": tipo,
        "idProyecto": idProyecto
    };
    $.ajax({
        type: "POST",
        url: url,
        data: parametros, // Adjuntar los campos del formulario enviado.
        success: function (data)
        {

            var res = data.split("***");
            // console.log(res[1]);
            $("#div2").html(res[0]); // Mostrar la respuestas del script PHP.
            $("#div3").html(res[1]);
        }
    });
}

function obtenerDatosSelect(value, variable, idSelect) {

    var parametros = {
        "seqProyectoPadre": value
    };
    $.ajax({
        data: parametros,
        url: 'contenidos/proyectos/contenidos/datosSelect.php',
        type: 'post',
        dataType: "json",
        success: function (response) {
            var select = $('#' + idSelect);
            $('#' + idSelect).empty();
            var options = select.attr('options');
            var selectedOption = '';
            $('#' + idSelect).append('<option value="0">Seleccione</option>');
            $.each(response, function (val, text) {

                $('#' + idSelect).append('<option value=' + val + '>' + text + '</option>');
            });
        }
    });


}

var fileActionUnit = function (name) {

    var div = name;
    // We can attach the `fileselect` event to all file inputs on the page

    $(document).on('change', ':file', function () {
        var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger(name, [numFiles, label]);
        console.log(name + " [" + numFiles + "," + label + "]");//
    });

    $(document).ready(function () {
        $(':file').on(div, function (event, numFiles, label) {
            // console.log("numFiles -> " + numFiles + " label ->" + label + " event ->" + event);
            var input = $(this).parents('.custom-file-input').find(':file'),
                    log = numFiles > 1 ? numFiles + ' Archivos seleccionados' : label;
            console.log("input.length -> " + input + " log ->" + log);//
            if (input.length) {
                //input.val(log);
            } else {
                if (log)
                    $("#" + name).text(log);
                //input.val(log);
            }

        });
    });
    removeFile(div, name);
}


function SubirInformes(form) {

    var idProyecto = $("#idProyecto").val();
    $("#seqInformes").addClass("required");
    $("#customFile").addClass("required");
    $("#seqInformes").css("border", "1px solid #red");
    $("#val_seqInformes").css("display", "inline");
    $("#val_customFile").css("display", "inline");

    if (validarCampos()) {

        $("#idProgress").show();
        var valor = 0;
        var formData = new FormData(document.getElementById(form));
        formData.append("dato", "valor");
        $.ajax({
            url: "contenidos/proyectos/contenidos/recibeArchivo.php",
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".progress-bar").css("width", "10%");
                // console.log("prueba1");
                valor = 90;
            },
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.onprogress = function (e) {
                    // For uploads
                    if (e.lengthComputable) {
                        valor = (e.loaded / e.total) * 100 - 10;
                        for (var i = 10; i < valor; i++) {
                            setTimeout(
                                    function () {
                                        $(".progress-bar").css("width", i + "%");
                                    }, 1000);
                        }
                    }
                };
                return xhr;
            },
            success: function (data) {
                setTimeout(
                        function () {
                            for (var i = 89; i <= 100; i++) {
                                //console.log("valor " + i);
                                $(".progress-bar").css("width", i + "%");
                                valor = i;
                            }
                        }, 100);

                setTimeout(
                        function () {
                            if (valor == 100) {
                                $("#multiUpload").html(data);
                            }
                        }, 1000);

                // Mostrar la respuestas del script PHP.
            },
            complete: function () {
                $("#seqGrupoGestion").val("0");
                $("#seqGestion").val("0");
                $("#txtComentario").val('');
                cargarContenido('contenido', './contenidos/proyectos/contenidos/datosLiquidacion.php?&seqProyecto=' + idProyecto + '&page=datosLiquidacion.php?tipo=3&id=5', '', true);
            }
        });
    }
}

function eliminarArchivo(archivo) {
    var idProyecto = $("#idProyecto").val();
    $("#seqInformes").removeClass("required");
    $("#customFile").removeClass("required");
    $("#seqInformes").css("border", "1px solid #ced4da");
    $("#val_seqInformes").css("display", "none");
    $("#val_customFile").css("display", "none");

    if (validarCampos()) {
        $("#mi-modal").modal('show');
        $("#modal-btn-si").on("click", function () {

            //alert("confirmo");
            var valor = 0;
            $("#idProgress").show();
            $(".progress-bar").css("width", "10%");
            valor = 10;
            var parametros = {
                "ruta": archivo,
                "idProyecto": $("#idProyecto").val(),
                "seqGestion": $("#seqGestion").val(),
                "seqPryEstadoProceso": $("#seqPryEstadoProceso").val(),
                "txtComentario": $("#txtComentario").val()
            };
            $.ajax({
                data: parametros,
                url: "contenidos/proyectos/contenidos/eliminaArchivo.php",
                type: 'post',
                dataType: "html",
                success: function (data) {
                    setTimeout(
                            function () {
                                for (var i = valor; i <= 100; i++) {
                                    //  console.log("valor " + i);
                                    $(".progress-bar").css("width", i + "%");
                                    valor = i;
                                }
                            }, 1000);

                    setTimeout(
                            function () {
                                if (valor == 100) {
                                    $("#multiUpload").html(data);
                                }
                            }, 1000);

                    // Mostrar la respuestas del script PHP.
                },
                complete: function () {
                    $("#seqGrupoGestion").val("0");
                    $("#seqGestion").val("0");
                    $("#txtComentario").val('');
                    $("#mi-modal").modal('hide');
                    cargarContenido('contenido', './contenidos/proyectos/contenidos/datosLiquidacion.php?&seqProyecto=' + idProyecto + '&page=datosLiquidacion.php?tipo=3&id=5', '', true);

                }
            });
        });
        $("#modal-btn-no").on("click", function () {
            $("#mi-modal").modal('hide');
        });
    }
}

function cerrarProyecto() {

    var idProyecto = $("#idProyecto").val();
    $("#seqInformes").removeClass("required");
    $("#customFile").removeClass("required");
    $("#seqInformes").css("border", "1px solid #ced4da");
    $("#val_seqInformes").css("display", "none");
    $("#val_customFile").css("display", "none");

    if (validarCampos()) {
        var parametros = {
            "seqProyecto": idProyecto,
            "seqGestion": $("#seqGestion").val(),
            "txtComentario": $("#txtComentario").val(),
            "seqPryEstadoProcesoAnt": $("#seqPryEstadoProceso").val()
        };
        $.ajax({
            data: parametros,
            url: "contenidos/proyectos/contenidos/cerrarProyecto.php",
            type: 'post',
            dataType: "html",
            success: function (data) {
                if (data != 'true') {
                    $("#mensajes").html(data);
                } else {
                    $("#mensajes").html("<div class='alert alert-danger' style='font-size: 12px'><strong>Success! </strong>Se realizo cierre del proyecto correctamente</div>");
                }

                // Mostrar la respuestas del script PHP.
            },
            complete: function () {

                cargarContenido('contenido', './contenidos/proyectos/contenidos/datosLiquidacion.php?&seqProyecto=' + idProyecto + '&page=datosLiquidacion.php?tipo=3&id=5', '', true);

            }
        });
    }
}

function mostrarOpcionInforme(id) {

    if ($("#" + id).val() == 'Otro') {
        $("#informeId").show();
        $("#txtInforme").addClass("required");
    } else {
        $("#informeId").hide();
        $("#txtInforme").removeClass("required");
    }

}

function descargarArchivo(url) {
    var bolCargando = true;
    if (bolCargando == 1) {
        var objCargando = obtenerObjetoCargando( );
    }
    objCargando.show();
    someterFormulario(
            'mensajes',
            'listadoExportable',
            url,
            true,
            false);
    setTimeout(function () {
        objCargando.hide();
    }, 57000);

}

var allDate = function () {
    $.each($("#frmProyectos input"), function (index, value) {
        var id = $(this).attr("id");
        if (id != 'undefined' && id != null && id != '') {
            if (id.split("fch") != null && id.split("fch") != 'undefined' && id.split("fch") != "" && id.split("fch")[1] != "") {
                var fch = id.split("fch");
                if (fch[1] != 'undefined' && fch[1] != null && fch[1] != '') {
                    // console.log("attr id:" + id);
                    $('#' + id).datetimepicker({
                        language: 'es',
                        format: 'yyyy-mm-dd',
                        weekStart: 1,
                        todayBtn: 1,
                        autoclose: 1,
                        todayHighlight: 1,
                        startView: 2,
                        minView: 2,
                        forceParse: 0
                    });
                }

            }
        }
    });
    eliminarObjeto("divCalendar");
    YAHOO.util.Event.onContentReady(
            "divCalendar",
            allDate
            );
}
YAHOO.util.Event.onContentReady(
        "divCalendar",
        allDate
        );

function configDate(id) {
    console.log("el id es:" + id)
    $('#' + id).datetimepicker({
        language: 'es',
        format: 'yyyy-mm-dd',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });


}

