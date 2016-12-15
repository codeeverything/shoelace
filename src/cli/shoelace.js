var request = require('request'),
    zlib = require('zlib'),
    fs = require('fs'),
    out = fs.createWriteStream('out');

// Fetch http://example.com/foo.gz, gunzip it and store the results in 'out'
request('http://192.168.33.21/src/packager.php?vagrant=basic-ubuntu&provision=ansible/basic-lamp&editorconfig=').pipe(zlib.createGunzip()).pipe(out);