<div class="container mt-4">

<h3>Buscar beneficiario</h3>

<input type="text" id="busqueda" class="form-control">

<table class="table mt-3">

<thead>
<tr>
<th>ID</th>
<th>Nombres</th>
<th>Apellidos</th>
<th></th>
</tr>
</thead>

<tbody id="resultado"></tbody>

</table>

<a href="/beneficiarios/create" class="btn btn-success">
+ Registrar nuevo beneficiario
</a>

</div>

<script>

document.getElementById("busqueda").addEventListener("keyup",function(){

let q = this.value;

fetch("/beneficiarios/buscarAjax?q="+q)
.then(r=>r.json())
.then(data=>{

let html="";

data.forEach(b=>{

html+=`
<tr>
<td>${b.id_digisalud}</td>
<td>${b.nombres}</td>
<td>${b.apellidos}</td>
<td>
<a href="/jornadas/1/asociar/${b.id}" class="btn btn-primary">
Asociar a jornada
</a>
</td>
</tr>
`;

});

document.getElementById("resultado").innerHTML = html;

});

});

</script>