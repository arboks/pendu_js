<?php
// retourne une ligne aléatoire du fichier $fileName
function rand_line($fileName) {
    do{
        $fileSize=filesize($fileName);
        $fp = fopen($fileName, 'r');
        fseek($fp, rand(0, $fileSize));
        $data = fread($fp, 30);  // toutes les lignes font moins de 30 caractères
        fclose($fp);
        $a = explode("\n",$data);
    }while(count($a)<2);
    return $a[1];
}

$result['word'] = rand_line("ressources/lexique_fr.txt"); 
$result['wordLength'] = strlen($result['word']);
$result['secretWord'] = str_repeat("_",$result['wordLength']);
$result['displaySecretWord'] = substr(str_repeat("_ ",$result['wordLength']), 0, -1); //remplace les charactères par des _


echo json_encode($result);
?>