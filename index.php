<?php
require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost','root','','curso_angular4');

$app->get("/pruebas",function() use($app){
	echo "Hola mundo desde slim PHP";
});

$app->get("/probando",function() use($app){
	echo "Otro texto cualquiera";
});


//LISTAR TODOS LOS PRODUCTOS
$app->get('/productos',function() use($db,$app){
	$sql = 'SELECT * FROM productos ORDER BY id DESC;';
	$query = $db->query($sql);

	$productos = array();
	while ($producto = $query->fetch_assoc()) {
		$productos[] = $producto;
	}

	$result = $arrayName = array(
		'status' => 'success',
		'code' => 200,
		'data' => $productos
	);
  echo json_encode($result);
});

//DEVOLVER UN SOLO PRODUCTO

//ELIMINAR UN PRODUCTO

//ACTUALIZAR UN PRODUCTO

//SUBIR UNA IMAGEN A UN PRODUCTO

//GUARDAR PRODUCTOS
$app->post('/productos', function() use ($app,$db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	if (!isset($data['nombre'])) {
		$data['nombre'] = NULL;
	}
	if (!isset($data['descripcion'])) {
		$data['descripcion'] = NULL;
	}
	if (!isset($data['precio'])) {
		$data['precio'] = NULL;
	}
	if (!isset($data['imagen'])) {
		$data['imagen'] = NULL;
	}		

	$nombre = $data['nombre'];
	$descripcion = $data['description'];
	$precio = $data['precio'];
	$imagen = $data['imagen'];

	$query = "INSERT INTO productos VALUES (NULL,'$nombre','$descripcion','$precio','$imagen')";

	$insert = $db->query($query);

		$result = $arrayName = array(
			'status' => 'error',
			'code' => 404,
			'message' => "Producto NO fue creado correctamente"
		);

	if($insert){
		$result = $arrayName = array(
			'status' => 'success',
			'code' => 200,
			'message' => "Producto creado correctamente"
		);
	}

    //echo $query;
	echo json_encode($result);
});

$app->run();