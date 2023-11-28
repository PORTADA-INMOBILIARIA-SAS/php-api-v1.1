<?php
header("Content-Type: application/json; charset=UTF-8");
if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on') {
    $url = "https://";
} else {
    $url = "http://";
}
$url .= $_SERVER['HTTP_HOST'];
$url .= $_SERVER["REQUEST_URI"];
$modules = [
	"test" => [
		"terminacion de contratos" => [
			"endpoint" => "$url".'terminaciones/',
			"suported params" => [
				"query params" => [
					"id" => "int",
					"search" => "text",
					"limit" => "int",
					"page" => "int"
				]
			]
		],
		"pqrs" => [
			"endpoint" => "$url".'pqrs/',
			"suported params" => [
				"query params" => [
					"id",
					"search",
					"limit",
					"page"
				]
			]
		]
	],
	"dev" => [
		"mantenimientos" => "$url".'mantenimientos/',
	],
	"env" => getenv("APPLICATION_ENVIRONMENT")
];
echo json_encode(["modules" => $modules], JSON_UNESCAPED_UNICODE);