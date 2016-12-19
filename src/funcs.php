<?php

/**
 * Given a source directory, recursively add all files (and sub dirs), to the ZIP
 * file in the destination directory
 *
 * @param string $src
 * @param string $dest
 * @param ZipArchive $zipfile
 */
function addFilesToZip($src, $dest, &$zipfile) {
    $dir = opendir($src);

    while (false !== ($file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                addFilesToZip($src . '/' . $file, $dest . '/' . $file, $zipfile);
            }
            else {
                if ($dest == '/') {
                    $dest = '';
                }

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

/**
 * Read a config for key from the global config (if any match)
 *
 * @param $key
 * @return null
 */
function getConfig($key) {
    global $globalConfig;   // TODO: Get rid of global dependency

    if (array_key_exists($key, $globalConfig['packages'])) {
        return $globalConfig['packages'][$key];
    }

    // should this be an error? or can we try to handle and then error if that fails?
    return null;
}

/**
 * Given a config key process this and return an array of package
 * dependencies
 *
 * @param string $configKey
 * @param ZipArchive $zip
 * @param array $returnConfig
 * @return array
 */
function processFromConfig($configKey, &$zip, &$returnConfig = []) {
    $config = getConfig($configKey);

    $returnConfig[] = $configKey;

    // if we're extending anything then get those configs as well
    if (array_key_exists('extends', $config)) {
        // process the dependency
        foreach ($config['extends'] as $extConfig) {
            processFromConfig($extConfig, $zip, $returnConfig);
        }
    }

    // check the environment and pull anything that needs
    if (array_key_exists('environment', $config)) {
        if (array_key_exists('vagrant', $config['environment'])) {
            foreach ($config['environment']['vagrant']['pull'] as $moarConfig) {
                processFromConfig($moarConfig, $zip, $returnConfig);
            }
        }
    }

    return $returnConfig;
}
