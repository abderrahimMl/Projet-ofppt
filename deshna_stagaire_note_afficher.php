 <?php echo "bien stgaiaire"; 

// require_once "conn.php";

// try{
// $sql="SELECT * FROM notes";
// $stmt=$conn->prepare($sql);
// $stmt->execute();
// $users=$stmt->fetchAll(PDO::FETCH_ASSOC);
// if(!empty($users)){
//     echo "<table border='1' >";
//     echo "<tr><th>id_stgiaire</th><th>Nom_stagaire</th><th>module</th><th>note1</th><th>note2</th><th>note3</th><th>note4</th><th>Efm</th><th>coef</th><th>moyenne</th></tr>";
//     foreach($users as $user){
//         echo "<tr>";
//         echo "<td>" . $user['id_stagiaire'] ."</td>";        
//         echo "<td>" . $user['nom_stagiaire'] ."</td>";
//         echo "<td>" . $user['id_course'] ."</td>";
//         echo "<td>" . $user['controle1'] ."</td>";        
//         echo "<td>" . $user['controle2'] ."</td>";
//         echo "<td>" . $user['controle3'] ."</td>";
//         echo "<td>" . $user['controle4'] ."</td>";
//         echo "<td>" . $user['EFM'] ."</td>";
//         echo "<td>" . $user['coef'] ."</td>";
//         echo "<td>" . $user['moyenne'] ."</td>";
//         echo "<td>" . "<a href='delete.php'>supprimer</a>" ."</td>";
//         echo "<td>" . "<a href='updateBaseDpnne.php'>Modifier</a>" ."</td>";
//         echo "</tr>";

//     }
//     echo "</table>";
// }else{
//     echo "base de donnes et vide ";
// }
// }catch(PDOException $e){
//     die("erreur " .$e->getMessage());
// }

?>
