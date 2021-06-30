<?php require("config.php");
include("headers.php");

if($_SERVER['REQUEST_METHOD'] != 'GET') : //si pas du GET
    // token expiré ?
    if ($now > $_SESSION['expiration']) :
        unset($_SESSION['token']);
        $response['error'] = "Access denied";
        echo json_encode($response);
        die();
    else :
        //renouvellement durée token
        $_SESSION['expiration'] = $now + 1 * 60;
    endif;
    //y a-til un token et est-il correct pour post
    if($_SERVER['REQUEST_METHOD'] == 'POST') :
        $token = $_POST;
    else : 
        //autre méthode
        parse_str(file_get_contents("php://input"), $token);
    endif;
    //token absent et différent du token de la session
    if( !isset($token['token']) OR $token['token'] != $_SESSION['token']) :
        $response['error'] = "Access denied";
        echo json_encode($response);
        die();
    endif;
endif;


$route = $_GET['display']; //(personnes, news =>table mysql)
//méthode GET

if($_SERVER['REQUEST_METHOD'] == 'GET') :
    //un seul record ?
    if (isset($_GET['id'])) :
        $id_field = "id_".$route;
        $sql = sprintf("SELECT * FROM $route WHERE $id_field = %d", $_GET['id']);
        $arrayDatas['message'] = "Détails de $route";
    else :
        //une liste d'élements ?
        $sql = "SELECT * FROM $route";
        $arrayDatas['message'] = "Liste des $route";
    endif;
endif;

if($_SERVER['REQUEST_METHOD'] == 'DELETE') :
    //un seul record ?
    if (isset($_GET['id'])) :
        $id_field = "id_".$route;
        $sql = sprintf("DELETE FROM $route WHERE $id_field = %d", $_GET['id']);
        $connect->query($sql);
        $arrayDatas['message'] = "Suppression de l'id ".$_GET['id']." sur $route";
        echo json_encode($arrayDatas);
        die();
    else :
        //si on n'a pas l'id
        $arrayDatas['error'] = "Il manque un id";
        //conversion et génération du json
        echo json_encode($arrayDatas);
        die();
    endif;
endif;

if($_SERVER['REQUEST_METHOD'] == 'POST') :
    //quelle route
    if($route == "personnes") :
            $sql = sprintf("INSERT INTO $route SET nom = '%s', prenom = '%s'",
                $_POST['nom'],
                $_POST['prenom']);
        else : 
            $sql = sprintf("INSERT INTO $route SET titre = '%s', contenu = '%s'",
                $_POST['titre'],
                $_POST['contenu']);
        endif;
        $connect->query($sql);
        echo $connect->error;
        $arrayDatas['message'] = "Ajout dans $route";
        $arrayDatas['id insert'] = $connect->insert_id;
        //conversion et énération du json
        echo json_encode($arrayDatas);
        die();
endif;

if($_SERVER['REQUEST_METHOD'] == 'PUT') :
    //a-ton un id ?
    if( isset($_GET['id'])) :
        //pas de $_PUT ou de $_PATCH, donc on récupère les datas de la requête http via :
        parse_str(file_get_contents("php://input"), $put);
        //print_r($put);
        //exit;
        //quelle route ?
        if($route == "personnes") :
            $sql = sprintf("UPDATE $route SET nom = '%s', prenom = '%s' WHERE id_personnes = %d",
                $put['nom'],
                $put['prenom'],
                $_GET['id']
            );
        else : 
            $sql = sprintf("UPDATE $route SET titre = '%s', contenu = '%s' WHERE id_news = %d",
                $put['titre'],
                $put['contenu'],
                $_GET['id']
            );
        endif;
        $connect->query($sql);
        echo $connect->error;
        $arrayDatas['message'] = "Edit id ".$_GET['id']." dans $route";
        //conversion et génération du json
        echo json_encode($arrayDatas);
        die();
    endif;
endif;

$req = $connect->query($sql);
echo $connect->error;
$arrayDatas['nbhits'] = $req->num_rows;

while($record = $req->fetch_object()) {
    $results[] = $record;
}
$arrayDatas['records'] = $results;
echo json_encode($arrayDatas);
exit;

echo'<pre>';
print_r($arrayDatas);

 include("debug.php");
 ?>