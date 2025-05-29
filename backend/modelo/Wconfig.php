<?php
date_default_timezone_set('America/Sao_Paulo');
function obterNomeProximaPasta() {
    // Obtém o caminho do documento atual
    $caminhoAtual = str_replace('\\', '/', realpath(dirname(__FILE__)));

    // Divide o caminho em partes
    $partesCaminho = explode('/', $caminhoAtual);

    // Obtém o nome da próxima pasta apenas se for localhost
    $proximaPasta = ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1') ? (isset($partesCaminho[3]) ? $partesCaminho[3] : '') : '';
    return $proximaPasta;
}

// Obtém o nome da próxima pasta
$nomeProximaPasta = obterNomeProximaPasta();

// Define as constantes do caminho do sistema
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/' . $nomeProximaPasta . '/');
define('SERVIDOR_PATH', $_SERVER['SERVER_NAME'] . '/' . $nomeProximaPasta . '/');


// Configurações do sistema
function obterSubdominioAtual() {
    $dominioCompleto = explode('.', $_SERVER['SERVER_NAME']);
    return $dominioCompleto[0];
}


define('MODELO_PATH', ROOT_PATH . 'backend/modelo/');
define('MODELO_PATH_ASAAS', ROOT_PATH . 'backend/modelo/asaas/');
define('MODELO_PATH_SISTEMA', ROOT_PATH . 'backend/modelo/sistema/');
define('MODELO_PATH_MODULO', ROOT_PATH . 'backend/modelo/modulo/');
define('PATH_AUTOLOAD', ROOT_PATH . 'backend/modelo/Autoload.php');
define('CONTROLE_PATH', ROOT_PATH . 'backend/controle/');
define('FILES_PATH', ROOT_PATH . 'backend/files/');
define('KEY_GOOGLE_MAPS', 'AIzaSyBxI_df9xhykCCBqN25N9IzsUFXKTsBBpA');
define('SECRET', '1234');
define('URL_MP', 'https://api.mercadopago.com/v1/payments');
define('TOKEN_MP2', 'APP_USR-ee73e666-072c-4316-8e30-a3f94877b0bd');
define('TOKEN_MP', 'APP_USR-4564219251051079-012021-938b05de9170866df0d46b8197425658-103005662');

?>
