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

include_once 'funcs.php';

// let's hack this out
$vagrant = $_GET['vagrant'];
$provisioner = $_GET['provision'];
$editorconfig = $_GET['editorconfig'];
$git = $_GET['git'];
$github = $_GET['github'];

// read in config
$globalConfig = json_decode(file_get_contents('../data/config.json'), true);

// setup a ZIP file to return the computed package
$zip = new ZipArchive();
$filename = "./build" . time() . rand(0, 1000) . ".zip";

if ($zip->open($filename, ZipArchive::CREATE) !== true) {
    exit("cannot open <$filename>\n");
}

// build an array of dependencies for provisioning
$dependencies = processFromConfig($provisioner, $zip);
$dependencies = array_reverse($dependencies);

// root directory the packages live in
$packageRoot = '../packages';

// assume we can't provision, or don't want to
$canProvision = false;

// build vagrant config
if ($vagrant) {
    // get and do something with a defined config
    $config = getConfig($vagrant);

    $sourceDir = $packageRoot . '/vagrant/';
    $destDir = '';

    if ($provisioner) {
        // get and do something with a defined config
        $config = getConfig($provisioner);

        $prov = explode('/', $provisioner);
        $system = $prov[0];
        $flavour = $prov[1];

        $sourceDir .= 'provisioned/' . $vagrant;
    } else {
        // basic Vagrant box with no provisioning
        $sourceDir .= 'basic/' . $vagrant;
    }

    if (file_exists($sourceDir)) {
        if ($prov) {
            $canProvision = true;
        }

        addFilesToZip($sourceDir, '/', $zip);
    }
}

// provisioning
if ($canProvision) {
    foreach ($dependencies as $provisioner) {
        $prov = explode('/', $provisioner);
        $system = $prov[0];
        $flavour = $prov[1];

        if ($prov) {
            $sourceDir = $packageRoot . "/provisioners/$system/$flavour";

            if (file_exists($sourceDir)) {
                addFilesToZip($sourceDir, '.shoelace/' . $system, $zip);
            }
        }
    }
}

// editorconfig config
if ($editorconfig == 'true') {
    $editorconfig = 'default/.editorconfig';

    addFilesToZip($packageRoot . '/editorconfig', '/', $zip);
}

// git config
if ($git == 'true') {
    addFilesToZip($packageRoot . '/git', '/', $zip);
}

// github config
if ($github == 'true') {
    addFilesToZip($packageRoot . '/github', '.github/', $zip);
}

// always include a README file
addFilesToZip($packageRoot . '/README', '/', $zip);

$zip->close();

// clear any debug output before we push the ZIP file
ob_clean();

// sent the ZIP file
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($filename));
header('Content-Disposition: attachment; filename="package.zip"');
readfile($filename);
unlink($filename);
