<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . '/../models/images_models.php';

class ImagesController {

    private $images_model;

    public function __construct() { 
        $this->images_model = new ImagesModel(); 
    }

    public function uploadImage() {
        if(!isset($_SESSION['user_id'])||($_SESSION['status']??'')!=='valide'){
            echo json_encode(["status"=>"error","message"=>"Vous devez être connecté et validé pour uploader."]); exit;
        }
        if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['image_input'])){
            $image=$_FILES['image_input'];
            $allowedTypes=["image/png","image/jpeg","image/webp"];
            if(!in_array($image['type'],$allowedTypes)){
                echo json_encode(["status"=>"error","message"=>"Type de fichier non supporté"]); return;
            }
            if(!is_uploaded_file($image['tmp_name'])){
                echo json_encode(["status"=>"error","message"=>"Fichier invalide"]); return;
            }
            if($image['size']>2*1024*1024){
                echo json_encode(["status"=>"error","message"=>"Fichier trop volumineux (>2Mo)"]); return;
            }
            list($w,$h)=getimagesize($image['tmp_name']);
            if($w<512||$h<512){
                echo json_encode(["status"=>"error","message"=>"Résolution minimale : 512x512"]); return;
            }
            $ext=pathinfo($image['name'],PATHINFO_EXTENSION);
            $uniqueName=uniqid('img_',true).".".$ext;
            $uploadDir=__DIR__.'/../uploads/';
            $uploadPath=$uploadDir.$uniqueName;
            if (move_uploaded_file($image['tmp_name'],$uploadPath)){
                $this->images_model->saveImageName($uniqueName,$_SESSION['user_id']);
                echo json_encode(["status"=>"success","file"=>$uniqueName,"message"=>"Image uploadée avec succès !"]);
            } else{
                echo json_encode(["status"=>"error","message"=>"Erreur lors de l'upload"]);
            }
        }
    }
}

$controller=new ImagesController();
if(isset($_POST['upload'])||isset($_FILES['image_input'])){
    $controller->uploadImage();
}
