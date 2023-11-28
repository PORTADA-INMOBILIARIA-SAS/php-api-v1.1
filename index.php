<?php
header("Content-Type: application/json; charset=UTF-8");
$modules = [
	"test" => [
		"terminacion de contratos" => [
			"endpoint" => "http://10.1.1.8/api/v1/terminaciones/",
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
			"endpoint" => "http://10.1.1.8/api/v1/pqrs/",
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
		"mantenimientos" => "http://10.1.1.8/api/v1/mantenimientos/",
	],
	"env" => getenv("APPLICATION_ENVIRONMENT")
];
echo json_encode(["modules" => $modules], JSON_UNESCAPED_UNICODE);