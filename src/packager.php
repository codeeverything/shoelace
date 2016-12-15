<?php

/**
 * This does the heavy lifting on the server side.
 *
 * It takes in details of the packages the user would like and returns a ZIP file containing
 * all the required files, in the correct directory structure.
 *
 * It also takes care of any relationship between the packages. For example, Vagrant without
 * provisioning should pull different a Vagrantfile to that with provisioning, and the
 * provisioner being used will also affect what is delivered.
 *
 * The same might be true for other config files. For example the editorconfig or gitignore
 * for a LAMP stack vs a Node stack might well differ.
 *
 * Differentiation is currently handled simply by splitting source files into different directories,
 * plus some small logic within. However this could be extended to "build" outputs with less
 * directories and possible duplication of common files/sections.
 *
 */

// `shoelace init --vagrant=basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`
// `shoelace init --box=[vagrant|ec2]/basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`
// 192.168.33.21/src/packager.php?vagrant=basic-ubuntu&provision=ansible/basic-lamp&editorconfig=

// let's hack this out
$vagrant = $_GET['vagrant'];
$provisioner = $_GET['provision'];
$editorconfig = $_GET['editorconfig'];
$git = $_GET['git'];
$github = $_GET['github'];

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
        $prov = explode('/', $provisioner);
        $system = $prov[0];
        $flavour = $prov[1];
        //echo "$system, $flavour";

        $sourceDir .= 'provisioned/' . $vagrant;
    } else {
        // basic Vagrant box with no provisioning
        $sourceDir .= 'basic/' . $vagrant;
    }

    //echo $sourceDir;
    //var_dump(file_exists($sourceDir));
    //die();

    if (file_exists($sourceDir)) {
        if ($prov) {
            $canProvision = true;
        }

        addFilesToZip($sourceDir, '/', $zip);
    }
}

if ($canProvision) {
    $prov = explode('/', $provisioner);
    $system = $prov[0];
    $flavour = $prov[1];


    if ($prov) {
        $sourceDir = $packageRoot . "/$system/$flavour";
        if (file_exists($sourceDir)) {
            addFilesToZip($sourceDir, '.shoelace/' . $system, $zip);
        }
    }
}

if ($editorconfig == 'true') {
    //if ($editorconfig == '') {
        $editorconfig = 'default/.editorconfig';
    //}

    //var_dump($editorconfig);

    addFilesToZip($packageRoot . '/editorconfig', '/', $zip);
}

if ($git == 'true') {
    addFilesToZip($packageRoot . '/git', '/', $zip);
}

if ($github == 'true') {
    addFilesToZip($packageRoot . '/github', '.github/', $zip);
}

// always include a README file
addFilesToZip($packageRoot . '/README', '/', $zip);

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
