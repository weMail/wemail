const fs = require('fs-extra');
const archiver = require('archiver');
const filesize = require('filesize');

// Read weMail release version from package.json
const package = JSON.parse(fs.readFileSync('package.json', 'utf8'));

// Set dir/zip name with release version
const wemail = `wemail.${package.version}`;
const dir = `build/${wemail}`;

// Remove any existing copied directory and make a new one
if (fs.existsSync(dir)) {
    fs.removeSync(dir);
}

fs.mkdirSync(dir);

// Copy files/directories into build/wemail.x.x.x directory
const sources = [
    'assets',
    'i18n',
    'includes',
    'vendor',
    'views',
    'composer.json',
    'wemail.php'
];

sources.forEach((src) => {
    fs.copySync(src, `${dir}/${src}`);
});

// Remove unnecessary files and folders
fs.removeSync(`${dir}/vendor/composer/installers`);
fs.removeSync(`${dir}/vendor/symfony/polyfill-mbstring/.git`);

// Create zip
const output = fs.createWriteStream(`build/${wemail}.zip`);
const archive = archiver('zip', {
    zlib: {
        level: 9
    }
});

// Event listner to show success message after finish zipping
output.on('close', function() {
    const size = filesize(archive.pointer(), {
        standard: "iec"
    });

    console.log(`${wemail}.zip created in build directory. File size: ${size}.`);

    // Delete build/wemail.x.x.x directory after zipping
    fs.removeSync(dir);
});

// Throw exception on error
archive.on('error', function(err) {
  throw err;
});

// Finish up zipping process
archive.pipe(output);
archive.directory(dir, wemail);
archive.finalize();
