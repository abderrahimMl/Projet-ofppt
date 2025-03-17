<?php
$servername="127.0.0.1";

$username="root";
$password="";
$db_name="projet_ofppt";
try{
    
    $conn=new PDO("mysql:host=$servername;dbname=$db_name;charset=utf8", $username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    

}catch(PDOException $e){
    die("erreur de connexion " .$e->getMessage());
}

?>