<?php 

/**
* http://www.php.net/manual/ru/function.curl-exec.php
*/
/**
* Send a GET request using cURL
* @param string $url to request
* @param array $get values to send
* @param array $options for cURL
* @return string
*/

function curl_get($url, array $get = NULL, array $options = array()) {
    $defaults = array(
        CURLOPT_URL => $url . (strpos($url, "?") === FALSE ? "?" : "") . http_build_query($get) ,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_DNS_USE_GLOBAL_CACHE => false,
        CURLOPT_SSL_VERIFYHOST => 0, //unsafe, but the fastest solution for the error " SSL certificate problem, verify that the CA cert is OK"
        CURLOPT_SSL_VERIFYPEER => 0, //unsafe, but the fastest solution for the error " SSL certificate problem, verify that the CA cert is OK"
    );
    $ch = curl_init();
    
    curl_setopt_array($ch, ($options + $defaults));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


    if (!$result = curl_exec($ch)) {
        trigger_error(curl_error($ch));
    }

    curl_close($ch);
    return $result;
}

$user_token = curl_get("https://iiko.biz:9900/api/0/auth/access_token", ['user_id' => "demoDelivery", "user_secret" => "PI1yFaKFCGvvJKi"]);

$user_token = substr($user_token, 1);

$user_token = substr($user_token, 0, strlen($user_token) - 1);

$organization_id_get = curl_get("https://iiko.biz:9900/api/0/organization/list", ["access_token" => $user_token, "request_timeout" => "00:02:00"]);

$organization_id_array = json_decode($organization_id_get);

$organization_id = $organization_id_array[0]->id;

$nomenclature = curl_get("https://iiko.biz:9900/api/0/nomenclature/{$organization_id}", ['access_token' => $user_token]);

$nomenclature_array = json_decode($nomenclature);

/*
echo "<pre>";
print_r($nomenclature_array->products);
echo "<pre>";
*/
?>
<style type="text/css">
	table td {
		padding: 20px;
		border: 1px solid #000;
	}
</style>
<table>
	<tr>
		<td>Артикул</td>
		<td>Название</td>
		<td>Цена</td>
		<td>Описание</td>
		<td>Вес:кг</td>
		<td>Энергетическая ценность</td>
	</tr>
	<? foreach ($nomenclature_array->products as $value): ?>
	<tr>
		<td><?= $value->code ?></td>
		<td><?= $value->name ?></td>
		<td><?= $value->price ?></td>
		<td><?= $value->description ?></td>
		<td><?= $value->weight ?></td>
		<td><?= $value->energyAmount ?></td>
	</tr>
	<? endforeach; ?>
</table>