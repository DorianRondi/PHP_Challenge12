<?php include('inc/head.php'); ?>

<?php
$filePath = "files";
// Créer un tableau qui assigne "dossier" ou "fichier" selon le type de contenue du dossier en entré. //
function directoryCrawler(string $filePath): array {
    $fileDirectory = opendir($filePath);
    $fichierETdossierCONVERTISstring = [];
    while ($file = readdir($fileDirectory)) {
        if (is_dir("$filePath/$file")) {
            $type = "dossier";
        } elseif (is_file("$filePath/$file")) {
            $type = "fichier";
        }
        $fichierETdossierCONVERTISstring[$file] = $type;
    }
    closedir($fileDirectory);
    return $fichierETdossierCONVERTISstring;
}
$crawler = directoryCrawler($filePath);
//$crawler = scandir($filePath);
// Créer un LI si le type de contenue est un "fichier". //
function arborescenceFichier(array $crawler, string $filePath)
{
    foreach ($crawler as $file => $type) {
        if (is_file("$filePath/$file")) {
            echo '<li><a href="?f=' . $filePath . DIRECTORY_SEPARATOR . $file . '">' . $file . '</a></li>';
        }
    }
}
// Créer un nouvelle UL avec le nom du contenue de type "dossier" en h2. //
function arborescenceDossier(array $crawler, string $filePath)
{
    foreach ($crawler as $file => $type) {
        if (is_dir("$filePath/$file") && !in_array($file, [".", ".."])) {
            echo '<a href="?d=' . $filePath . DIRECTORY_SEPARATOR . $file . '"><h2>' . $file . '</h2></a><ul>';
            $newPath = $filePath . DIRECTORY_SEPARATOR . $file;
            $newCrawler = directoryCrawler($newPath);
            //$newCrawler = scandir($newPath);
            arborescenceFichier($newCrawler, $newPath);
            arborescenceDossier($newCrawler, $newPath);
            echo '</ul>';
        }
    }
}
// https://openclassrooms.com/forum/sujet/php-supprimer-dossier-sous-dossier-et-fichier //
function RemoveAll ( $path ) {
    foreach ( new DirectoryIterator($path) as $item ):
        if ( $item->isFile() ) unlink($item->getRealPath());
        if ( !$item->isDot() && $item->isDir() ) RemoveAll($item->getRealPath());
    endforeach;
    rmdir($path);
}
if(isset($_GET["f"])){
    $fichier = $_GET["f"];
    $contenu = file_get_contents($fichier);
}
if(isset($_POST["deleteDIR"])){
    $fichier = $_POST["fichierAmodifier"].DIRECTORY_SEPARATOR;
    RemoveAll($fichier);
}
if(isset($_POST["delete"])){
    $fichier = $_POST["fichierAmodifier"];
    unlink($fichier);
}
if(isset($_POST["modifZone"]) && ($_POST["delete"] != "on")){
    $fichier = $_POST["fichierAmodifier"];
    $file = fopen($fichier,"w");
    fwrite($file,$_POST["modifZone"]);
    fclose($file);
    /*header("location:index.php");
    exit();*/
}
// @ devant une fonction permet de caché les erreurs. //
$ext = @pathinfo($_GET["f"], PATHINFO_EXTENSION);
?>
<form action="index.php" method="POST">
    <?php
    switch($ext){
        case "txt":
        case "doc":
        case "html":
    ?>
    <input type="hidden" name="fichierAmodifier" value="<?= $_GET["f"]; ?>">
    <textarea name="modifZone" id="modifZone" cols="110" rows="5">
        <?php echo $contenu; ?>
    </textarea>
    <fieldset>
        <input type="submit">
        <input type="radio" id="supp" name="delete">
        <label for="supp">Supprimer le document</label>
    </fieldset>
    <?php
            break;
            case "jpg":
            case "jpeg":
            case "bmp":
            case "png":
    ?>
    <input type="hidden" name="fichierAmodifier" value="<?= $_GET["f"]; ?>">
    <fieldset>
        <input type="submit">
        <input type="radio" id="supp" name="delete">
        <label for="supp">Supprimer le document</label>
    </fieldset>
    <img src="<?= $_GET["f"] ?>" alt="">
    <?php
        break;
        case "":
    ?>
    <input type="hidden" name="fichierAmodifier" value="<?= $_GET["d"]; ?>">
    <fieldset>
        <input type="submit" value="supprim">
        <label for="supp">Supprimer le dossier et tout son contenue ?</label>
        <input type="hidden" id="supp" name="deleteDIR">
    </fieldset>
    <?php
            break;
        default:
            break;
    }
    ?>
</form>
<?php
echo '<a href="?d='.$filePath.'"><h2>'.$filePath.'</h2></a>';
echo '<ul>';
arborescenceFichier($crawler, $filePath);
arborescenceDossier($crawler, $filePath);
echo '</ul>';
?>
<?php include('inc/foot.php'); ?>