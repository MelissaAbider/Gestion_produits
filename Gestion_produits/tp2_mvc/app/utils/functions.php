<?php


//fonction pour rediriger vers une page avec des paramètres

function redirect($action = 'home', $params = [])
{
    $url = 'index.php?action=' . $action;

    // ajout des paramètres supplémentaires à l'URL
    foreach ($params as $key => $value) {
        $url .= '&' . $key . '=' . urlencode($value);
    }

    // redirection
    header('Location: ' . $url);
    exit;
}


//fonction pour nettoyer les données d'entrée

function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


//fonction pour construire une URL avec les paramètres actuels et une action spécifique
function buildActionUrl($action, $params = [])
{
    $urlParams = $params;
    $urlParams['action'] = $action;
    return '?' . http_build_query($urlParams);
}
