<?php
header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Origin: http://localhost:5173");
// header("Access-Control-Opener-Policy: same-origin");
// header("Access-Control-Embedder-Policy: require-corp");

$debug = [];
// $debug["headers"] = getallheaders();
// $debug["Authorization2"] = $debug["headers"]["Authorization2"];
// $debug["glbals"] = $GLOBALS;
// $debug["env"] = $_ENV;
$method = $_SERVER['REQUEST_METHOD'];
if ($method === "OPTIONS") {
	http_response_code(200);
	return;
}

require_once "vendor/autoload.php";
require_once "src/clases/validate.php";
require_once "src/api_final.php";

$authorization = new validated();
// $validated = $authorization->valid();
$validated = $authorization->valid_res_api_key();
// $debug["validated"] = $validated;
if (!isset($validated) || !isset($validated["code"]) || $validated["code"] !== 200) {
	$err["Unauthorized"] = "Bad getway";
	$debug["Errors"][] = $err;
	http_response_code($validated["code"] ?? 501);
	echo json_encode($err, JSON_UNESCAPED_UNICODE);
	//	echo json_encode($debug, JSON_UNESCAPED_UNICODE);
	return;
}

switch ($method) {
	case 'GET':
		$debug["GET"] = $_GET;
		try {

			$limit_int = intval($_GET['limit'] ?? 10);
			$page_int = intval($_GET['page'] ?? 1);

			$api_REST = new api_final();

			// consulta por id
			if (isset($_GET['id']) && !empty($_GET['id'])) {

				$id = $_GET['id'];

				$result = $api_REST->get_by_id($id);

				if (isset($result["data"])) {

					http_response_code($result["code"] ?? 200);
					// $result["debug"] = $debug;
					echo json_encode(["data" => $result["data"]], JSON_UNESCAPED_UNICODE);

					return;
				} else {

					http_response_code($result["code"] ?? 500);
					// $result["debug"] = $debug;
					echo json_encode(array('message' => 'Algo salio mal. ' . $result['message']), JSON_UNESCAPED_UNICODE);

					return;
				}
			}

			// consulta por match
			if (isset($_GET['search']) && !empty($_GET['search'])) {

				$result = $api_REST->get_by_match($_GET['search']);
				http_response_code($result["code"] ?? 500);

				if (isset($result["debug"])) {

					// $result["debug"] = $debug;
					echo json_encode($result, JSON_UNESCAPED_UNICODE);

					return;
				}

				// $result["debug"] = $debug;
				echo json_encode($result["res"], JSON_UNESCAPED_UNICODE);

				return;
			}

			// consulta general
			$result = $api_REST->get_all($limit_int, $page_int);
			// $result["debug"] = $debug;

			if ($result) {

				http_response_code(200);
				echo json_encode($result, JSON_UNESCAPED_UNICODE);

				return;
			} else {

				$res["message"] = "Algo salio mal.";
				// $res["debug"] = $debug;
				http_response_code(500);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);

				return;
			}

			return;
		} catch (\Throwable $th) {

			$res["message"] = $th->getMessage();
			// $res["debug"] = $debug;
			http_response_code(500);
			echo json_encode($res, JSON_UNESCAPED_UNICODE);

			return;
		}
		break;

	case 'POST':
		try {
			// TODO esto no tiene returns xd
			$json_data = file_get_contents("php://input");
			// $debug["json_data"] = $json_data;

			if (!$json_data) {

				$res["message"] = "No se enviaron datos";
				// $res["debug"] = $debug;
				http_response_code(400);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				break;
			}

			$data = json_decode($json_data, true);
			// $debug["data"] = $data;

			$api_REST = new api_final();
			$id = $api_REST->insert($data);
			// $debug["id"] = $id;

			if ($id["code"] === 201) {
				$res["message"] = "Insertado correctamente.";
				$res["new_id"] = $id["id"];
				// $res["debug"] = $debug;
				http_response_code(201);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
			} else {
				$res["message"] = "Error al insertar.";
				// $res["debug"] = $debug;
				http_response_code(500);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
			}
		} catch (\Throwable $th) {
			$res["message"] = $th->getMessage();
			// $res["debug"] = $debug;
			http_response_code(500);
			echo json_encode($res, JSON_UNESCAPED_UNICODE);
		}
		break;

	case 'PUT':
		try {
			$json_data = file_get_contents("php://input");
			// $debug["json_data"] = $json_data;
			$data = json_decode($json_data, true);
			// $debug["data"] = $data;
			$api_REST = new api_final();
			$result = $api_REST->update($data);
			// $result = $debug;
			if ($result) {
				$res["message"] = "Actualizado correctamente.";
				$res["updated_info"] = $result["updated_info"]["data"] ?? $result;
				// $res["debug"] = $debug;
				http_response_code(200);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				return;
			} else {
				$res["message"] = "Error al actualizar.";
				http_response_code(500);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				return;
			}
			return;
		} catch (\Throwable $th) {
			$res["message"] = $th->getMessage();
			// $res["debug"] = $debug;
			http_response_code(500);
			echo json_encode($res, JSON_UNESCAPED_UNICODE);
			return;
		}
		break;

	case 'DELETE':
		try {
			// FIX mal implementacion de metodos. No combinar DELETE con GET... basicamente
			$json_data = file_get_contents("php://input");
			// $debug["json_data"] = $json_data;
			$data = json_decode($json_data, true);
			// $debug["data"] = $data;
			if (isset($data["id"])) {

				$id = $data["id"];
				// $debug["id type"] = gettype($id);
				if (!is_int($id)) {
					$res["message"] = "El parámetro 'id' debe ser un entero.";
					// $res["debug"] = $debug;
					http_response_code(400);
					echo json_encode($res, JSON_UNESCAPED_UNICODE);
					return;
				}
				$api_REST = new api_final();
				$result = $api_REST->delete($id);
				// $result = $debug;

				if ($result) {
					// $result["debug"][] = $debug;
					http_response_code($result["code"]);
					echo json_encode($result, JSON_UNESCAPED_UNICODE);
					return;
				} else {
					$res["message"] = "Error al eliminar.";
					// $res["debug"] = $debug;
					http_response_code(500);
					echo json_encode($res, JSON_UNESCAPED_UNICODE);
					return;
				}
			} else {
				$res["message"] = "El parámetro 'id' es requerido para la eliminación.";
				// $res["debug"] = $debug;
				http_response_code(400);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				return;
			}
		} catch (\Throwable $th) {
			$res["message"] = $th->getMessage();
			// $res["debug"] = $debug;
			http_response_code(500);
			echo json_encode($res, JSON_UNESCAPED_UNICODE);
			return;
		}
		break;

	default:
		$res["message"] = "Método no permitido.";
		http_response_code(405); // Method Not Allowed
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		break;
}