
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
</head>
<body style="background-color: #F2F6EE">

	<style type="text/css" media="screen">
		:root{
		  --verde: #94AA4F;
		  --gris-text:#595959E5;
		  --gris-fondo:#F2F6EE;
		  --gris-text-light:#595959CC;
		}
	.card-report{
		width: 100%;
		border-radius: 10px;
		margin: 10px 5px;
		padding: 10px 15px;
		background-color: white;
		box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
	}

		.card-body{
			display: grid;
			padding: 1rem 1rem;
		}
		.bg-gris{
			background-color: var(--gris-fondo);
		}

	</style>

	<div>
	  <div class="card-report">
	    <div class="card-body grid-col-2x grid-col-2x-sm">
	    	<h3>Hola!, {{$datos->nombre}}</h3>
	    	<h3>Esta es tu contraseña provisoria para continuar con el cambio de tu contraseña:</h3>

	    	<h2 style="color:#94AA4F">CLAVE: <span style="color:#595959CC"> {{ $datos->clave }} </span></h2>
	    </div>
	  </div>
	</div>
</body>
</html>


