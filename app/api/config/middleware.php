<?php

return [
	'alias' => [
		'JwtAuth'	=>	app\api\middleware\JwtAuth::class,
		'CaptchaAuth'=>	app\api\middleware\CaptchaAuth::class,
	],	
];
