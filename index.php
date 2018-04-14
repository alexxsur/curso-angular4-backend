<?php
require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost','root','','curso_angular4');

// Configuración de cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

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
$app->get('/producto/:id',function($id) use($db,$app){
	$sql = 'SELECT * FROM productos WHERE id = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => "Producto NO disponible"
	);

	if ($query->num_rows == 1) {
		$producto = $query->fetch_assoc();
		$result = $arrayName = array(
			'status' => 'success',
			'code' => 200,
			'data' => $producto
		);
	}
   echo json_encode($result);
});

//ELIMINAR UN PRODUCTO
$app->get('/delete-producto/:id',function($id) use($db,$app){
	$sql = 'DELETE FROM productos WHERE id = '.$id;
	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => "El producto se ha eliminado correctamente!!"
		);
	}else{

		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => "El producto no se ha eliminado!!"
		);
	}


   echo json_encode($result);
});

//ACTUALIZAR UN PRODUCTO
$app->post('/update-producto/:id', function($id) use ($app,$db){
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
	$descripcion = $data['descripcion'];
	$precio = $data['precio'];
	$imagen = $data['imagen'];

	$sql = "UPDATE productos SET nombre = '$nombre', descripcion = '$descripcion',
	precio = '$precio', imagen = '$imagen' WHERE id = $id;";

	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => "Producto actualizado correctamente"
		);
	}else{
		$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => "Producto NO fue actualizado correctamente"
	);
	}
	echo json_encode($result);
});

//SUBIR UNA IMAGEN A UN PRODUCTO
$app->post('/upload-file',function() use ($db,$app){

	if (isset($_FILES['uploads'])) {
      $piramideUploader = new PiramideUploader();
      $upload = $piramideUploader->upload("image","uploads","uploads",array('image/jpeg','image/jpg','image/png','image/gif'));
      $file = $piramideUploader->getInfoFile();
      $file_name = $file['complete_name'];

      if (isset($upload) && $upload["uploaded"] == false) {
  		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => "El archivo no ha podido subirse"
		);
      }else{
	      	$result = array(
				'status' => 'success',
				'code' => 200,
				'message' => "El archivo se ha subido",
				'filename' => $file_name
			);
      }
	}
	echo json_encode($result);
});

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
	$descripcion = $data['descripcion'];
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