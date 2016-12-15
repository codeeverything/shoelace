<?php

// `shoelace init --vagrant=basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`

// let's hack this out
$vagrant = $_GET['vagrant'];
$provisioner = $_GET['provision'];
$editorconfig = $_GET['editorconfig'];

$zip = new ZipArchive();
$filename = "./build" . time() . rand(0, 1000) . ".zip";

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$packageRoot = '../packages';

$canProvision = false;
if ($vagrant) {

    $sourceDir = $packageRoot . '/vagrant/';
    $destDir = '';

    if ($provisioner) {
        $canProvision = true;

        $prov = explode('/', $provisioner);
        $system = $prov[0];
        $flavour = $prov[1];
        //echo "$system, $flavour";

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

    addFilesToZip($packageRoot . "/$system/$flavour", '.shoelace/' . $system, $zip);
}

if (isset($editorconfig)) {
    if ($editorconfig == '') {
        $editorconfig = 'default/.editorconfig';
    }

    //var_dump($editorconfig);

    addFilesToZip($packageRoot . '/editorconfig', '/', $zip);
}

$zip->close();

header('Content-Type: application/zip');
header('Content-Length: ' . filesize($filename));
header('Content-Disposition: attachment; filename="package.zip"');
readfile($filename);
unlink($filename);

/**
 * Given a source directory, recursively add all files (and sub dirs), to the ZIP
 * file in the destination directory
 *
 * @param $src
 * @param $dest
 * @param $zipfile
 */
function addFilesToZip($src, $dest, &$zipfile) {
    //echo "../" . $src;
    $dir = opendir($src);

    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            //var_dump(is_dir($src . '/' . $file));
            if ( is_dir($src . '/' . $file) ) {
                addFilesToZip($src . '/' . $file, $dest . '/' . $file, $zipfile);
            }
            else {
                if ($dest == '/') {
                    $dest = '';
                }

                //echo "adding $src/$file to project/$dest/$file<br/>";

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