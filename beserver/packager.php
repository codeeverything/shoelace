<?php

// `shoelace init --vagrant=basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`

// let's hack this out
print_r($_GET);
$vagrant = $_GET['vagrant'];
$provisioner = $_GET['provision'];
$editorconfig = $_GET['editorconfig'];

$zip = new ZipArchive();
$filename = "./build" . time() . rand(0, 1000) . ".zip";

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

//$zip->addFromString("test1/testfilephp.txt" . time(), "#1 This is a test string added as testfilephp.txt.\n");
//$zip->addFromString("test1/testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
//$zip->addFromString("test2/testfilephp1.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
//$zip->addFromString("testfilephp3.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
////$zip->addFile($thisdir . "/too.php","/testfromfile.php");
//echo "numfiles: " . $zip->numFiles . "\n";
//echo "status:" . $zip->status . "\n";
//$zip->close();

$canProvision = false;
if ($vagrant) {

    $sourceDir = '../vagrant/';
    $destDir = '';

    if ($provisioner) {
        $canProvision = true;

        $prov = explode('/', $provisioner);
        $system = $prov[0];
        $flavour = $prov[1];
        echo "$system, $flavour";

        $sourceDir .= 'provisioned/' . $vagrant;
    } else {
        // basic Vagrant box with no provisioning
        $sourceDir .= 'basic/' . $vagrant;
    }

    addFilesToZip($sourceDir, '/', $zip);
}

if ($canProvision) {
    $prov = explode('/', $provisioner);
    $system = $prov[0];
    $flavour = $prov[1];

    addFilesToZip("../$system/$flavour", '.shoelace/' . $system, $zip);
}

if (isset($editorconfig)) {
    if ($editorconfig == '') {
        $editorconfig = 'default/.editorconfig';
    }

    var_dump($editorconfig);

    addFilesToZip('../editorconfig', '/', $zip);
}

$zip->close();

function addFilesToZip($src, $dest, &$zipfile) {
    //echo "../" . $src;
    $dir = opendir($src);

    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            var_dump(is_dir($src . '/' . $file));
            if ( is_dir($src . '/' . $file) ) {
                addFilesToZip($src . '/' . $file, $dest . '/' . $file, $zipfile);
            }
            else {
                if ($dest == '/') {
                    $dest = '';
                }

                echo "adding $src/$file to project/$dest/$file<br/>";

                if ($dest) {
                    $zipfile->addFile($src . "/$file", "$dest/$file");
                } else {
                    $zipfile->addFile($src . "/$file", "$file");
                }

            }
        }
    }
    closedir($dir);
}