<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Models\User;

function setSession( $key, $value)
{
    session()->put($key, $value);

    return true;
}


function getSession( $key )
{
    return session($key, null);
}

function sessionMessage($type, $message)
{
    Session::flash($type, $message); 
}


function codeEncrypt( ?string $value )
{
    $char = ['P','Q','R','S','T','U','V','W','X','Y','Z'];

    $code = $char[rand(0,10)] . $char[rand(0,10)] . $char[rand(0,10)];
    $code.= rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    $code.= $char[rand(0,10)] . $char[rand(0,10)] . $char[rand(0,10)] . $char[rand(0,10)] ;

    return $code;
}

function codeDecrypt( $value=null )
{
    if(!$value)
        return null;

    $code = substr($value, 3, -4);

    $code = base64_decode(str_pad(strtr($code, '-_', '+/'), strlen($code) % 4, '=', STR_PAD_RIGHT));

    return $code;
}

function batchInsert($deliveries, $class)
{
    DB::beginTransaction();
    try {
        foreach (array_chunk($deliveries,1000) as $deliveriesChunk) {

            $class::insert($deliveriesChunk);
        }
    }
    catch (Exception $e){
        echo("ERRO batch insert {$class}".$e->getMessage());
        DB::rollback();
        return false;
    }
    DB::commit();

    return true;
}


function getAuthUserId()
{
    return Auth::id() ?? 0;
}

function urlExists($url){
    $headers = get_headers($url);
    return stripos($headers[0],"200 OK") ? true : false;
}


function errorInfo($err)
{
    if(hasScope("Super"))
        return "{$err->getMessage()} {$err->getFile()} {$err->getLine()}";

    return "{$err->getMessage()}";
}

function hasScope($scope)
{
    return User::hasScope($scope);
}


function hasProfile($scope)
{
    return User::hasProfile($scope);
}


function getInicialRoute()
{
    return hasProfile('Retail') ? 'dash-retail' : 'dash-adv';
}


function errorMessage($err)
{
    return "\n{$err->getMessage()} {$err->getFile()} {$err->getLine()}";
}


function getCnpjInfoApi ($cnpj) {
        
    $cnpj = preg_replace( '/[^0-9]/', '', $cnpj );

    if ( strlen( $cnpj ) === 11 ) {
        return false;
    } else {
        $token="de267d4af60430ca9280bf5d5b3d08624301f5677d8d7e13fc2aca430fd6a738";
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.receitaws.com.br/v1/cnpj/$cnpj",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $token"
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }

    $response = json_decode($response);

    if(isset($response->status) && $response->status == "ERROR")
        return false;

    return $response;
}


function getCepApi ($cep)
{
    $cep = preg_replace( '/[^0-9]/', '', $cep );

    if ( strlen( $cep ) != 8 ) {
        $response = 'null';
    } else {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://viacep.com.br/ws/$cep/json/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }

    return $response;
}


function collectToArray($collection, $key, $value)
{
    $array = [];
    foreach($collection as $obj)
    {
        $array[$obj->$key] = $obj->$value;
    }
    return $array;
}


function costCalc($cpm, $viewers)
{
    return ($cpm * $viewers) / 1000;
}


function cpmCalc($cost, $viewers)
{
    if($viewers == 0)
        return 0;

    return $cost/($viewers/1000);
}


function getUserRoute($route)
{
    return User::getRoute($route);
}


function modifyHourByString($stringTime, $modify)
{
    $date = new DateTime($stringTime);
    $date->modify("$modify hour");
    return $date->format('H:i:s');
}

function datetimesFromDate($date)
{
    $dateTimes = [];
    $dateTimes[] = $date->format("Y-m-d H:i:s");

    for ($i=0; $i < 23; $i++) { 
        $dateTimes[] = $date->modify("+1 hour")->format("Y-m-d H:i:s");
    }

    return $dateTimes;
}


// ***
// FRONT 

function title( $id = null )
{
    $title = ($id) ? "Editar" : "Criar";

    return $title;
}

function classForm()
{   
    $class = "form-control";

    return $class;
}

function classFormDiv( $size )
{   
    $class = "form-group col-md-$size";
    return $class;
}


function formatMoney ($value):String
{
    $result = 'R$ ' . number_format($value, 2, ',', '.');
    return $result;
}
function formatThousand ($value)
{
    $result = number_format($value, 0, "", ".");

    return $result;
}
function formatPercent($number, $integer = false)
{
    $decimal = ($integer) ? 0 : 2;
    return number_format( ($number *100) , $decimal, '.', '');
}
function space($n) {
    for($i=0; $i<$n; $i++) {
        echo "&nbsp;";
    }
}

function removeAccents( $string )
{
    $result = str_replace ( 
        array(' ',',','à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý'),
        array('-','','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y'),
        $string
    );
    return strtolower($result);
}

function strLimit( $string, $limit=30 )
{
    $newString = (strlen($string) > $limit) ? substr($string, 0, $limit)."..." : $string;

    return strtolower($newString);
}
?>