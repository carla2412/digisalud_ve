<div class="row">

<div class="col-md-6">
<label>Nombres</label>
<input name="nombres" class="form-control" required>
</div>

<div class="col-md-6">
<label>Apellidos</label>
<input name="apellidos" class="form-control" required>
</div>

<div class="col-md-4">
<label>Fecha nacimiento</label>
<input type="date" name="fecha_nacimiento" class="form-control" required>
</div>

<div class="col-md-4">
<label>Sexo</label>
<select name="sexo" class="form-control">
<option value="M">M</option>
<option value="F">F</option>
</select>
</div>

<div class="col-md-4">
<label>Pais nacimiento</label>
<select id="pais_nacimiento" name="pais_nacimiento" class="form-control"></select>
</div>

<div class="col-md-6">
<label>Telefono</label>
<input name="telefono" class="form-control">
</div>

<div class="col-md-6">
<label>Correo</label>
<input name="correo" class="form-control">
</div>

</div>

<hr>

<label>
<input type="checkbox" id="chkDireccion" name="direccion_activa">
Dirección
</label>

<div id="direccionBox" style="display:none">

<input name="pais" class="form-control mt-2" placeholder="Pais">
<input name="estado" class="form-control mt-2" placeholder="Estado">
<input name="municipio" class="form-control mt-2" placeholder="Municipio">
<input name="parroquia" class="form-control mt-2" placeholder="Parroquia">
<input name="ciudad" class="form-control mt-2" placeholder="Ciudad">

</div>

<hr>

<label>
<input type="checkbox" id="chkEscolaridad" name="escolaridad_activa">
Escolaridad
</label>

<div id="escolaridadBox" style="display:none">

<input name="nombre_escuela" class="form-control mt-2">
<input name="grado" class="form-control mt-2">
<input name="seccion" class="form-control mt-2">
<input name="turno" class="form-control mt-2">

</div>

<script>

document.getElementById("chkDireccion").addEventListener("change",function(){

document.getElementById("direccionBox").style.display =
this.checked ? "block" : "none";

});

document.getElementById("chkEscolaridad").addEventListener("change",function(){

document.getElementById("escolaridadBox").style.display =
this.checked ? "block" : "none";

});

</script>