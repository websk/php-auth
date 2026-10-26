import {access, cp, mkdir, mkdtemp, readFile, readdir, rm, stat} from 'node:fs/promises';
import {createHash} from 'node:crypto';
import {tmpdir} from 'node:os';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const nodeModulesRoot = path.join(projectRoot, 'node_modules');
const assetsRoot = path.join(projectRoot, 'public/assets/libraries');

const copies = [
    ['blueimp-file-upload/css/jquery.fileupload.css', 'blueimp-file-upload/css/jquery.fileupload.css'],
    ['blueimp-file-upload/js/jquery.fileupload.js', 'blueimp-file-upload/js/jquery.fileupload.js'],
    ['blueimp-file-upload/js/jquery.fileupload-process.js', 'blueimp-file-upload/js/jquery.fileupload-process.js'],
    ['blueimp-file-upload/js/jquery.fileupload-validate.js', 'blueimp-file-upload/js/jquery.fileupload-validate.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js', 'bootstrap/js/bootstrap.bundle.min.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js', 'bootstrap/js/bootstrap.min.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js.map', 'bootstrap/js/bootstrap.bundle.min.js.map'],
    ['bootstrap/dist/css/bootstrap.min.css', 'bootstrap/css/bootstrap.min.css'],
    ['bootstrap/dist/css/bootstrap.min.css.map', 'bootstrap/css/bootstrap.min.css.map'],
    ['jquery/dist/jquery.min.js', 'jquery/jquery.min.js'],
    ['jquery-ui/dist/jquery-ui.min.js', 'jquery-ui/jquery-ui.min.js'],
    ['jquery-ui/dist/themes/base/jquery-ui.min.css', 'jquery-ui/themes/base/jquery-ui.min.css'],
    ['jquery-ui/dist/themes/base/images', 'jquery-ui/themes/base/images'],
    ['jquery-validation/dist/jquery.validate.min.js', 'jquery-validation/jquery.validate.min.js'],
    ['@fortawesome/fontawesome-free/css/all.min.css', 'font-awesome/css/all.min.css'],
    ['@fortawesome/fontawesome-free/css/v4-shims.min.css', 'font-awesome/css/v4-shims.min.css'],
    ['@fortawesome/fontawesome-free/webfonts', 'font-awesome/webfonts'],
];

const projectCopies = [
    ['vendor/websk/php-crud/resources/css/crud.css', 'crud/crud.css'],
];

async function copyFiles(targetRoot) {
    await rm(targetRoot, {recursive: true, force: true});
    await mkdir(targetRoot, {recursive: true});

    for (const [source, destination] of [...copies, ...projectCopies]) {
        const sourcePath = path.join(source.startsWith('vendor/') ? projectRoot : nodeModulesRoot, source);
        await access(sourcePath);
        const destinationPath = path.join(targetRoot, destination);
        const sourceStat = await stat(sourcePath);
        await mkdir(path.dirname(destinationPath), {recursive: true});
        await cp(sourcePath, destinationPath, {recursive: sourceStat.isDirectory(), force: true});
    }
}

async function listFiles(root) {
    const files = [];
    async function walk(directory) {
        for (const entry of await readdir(directory, {withFileTypes: true})) {
            const absolutePath = path.join(directory, entry.name);
            if (entry.isDirectory()) {
                await walk(absolutePath);
            } else if (entry.isFile()) {
                files.push(path.relative(root, absolutePath));
            }
        }
    }
    await walk(root);
    return files.sort();
}

async function digest(filePath) {
    return createHash('sha256').update(await readFile(filePath)).digest('hex');
}

async function assertSourceMapsExist(root) {
    const rootPath = path.resolve(root);
    for (const file of await listFiles(root)) {
        if (!file.endsWith('.css') && !file.endsWith('.js')) {
            continue;
        }
        const contents = await readFile(path.join(root, file), 'utf8');
        for (const match of contents.matchAll(/sourceMappingURL=([^\s*]+)/g)) {
            const reference = match[1].split(/[?#]/, 1)[0];
            if (reference.startsWith('data:') || reference.includes('://')) {
                continue;
            }
            const sourceMapPath = path.resolve(path.dirname(path.join(root, file)), reference);
            if (sourceMapPath !== rootPath && !sourceMapPath.startsWith(rootPath + path.sep)) {
                throw new Error(`Source map escapes the assets directory: ${file} -> ${reference}`);
            }
            try {
                await access(sourceMapPath);
            } catch {
                throw new Error(`Missing source map: ${file} -> ${reference}`);
            }
        }
    }
}

async function verifyAssets() {
    const temporaryRoot = await mkdtemp(path.join(tmpdir(), 'php-auth-assets-'));
    try {
        await copyFiles(temporaryRoot);
        const expectedFiles = await listFiles(temporaryRoot);
        const actualFiles = await listFiles(assetsRoot);
        if (expectedFiles.join('\n') !== actualFiles.join('\n')) {
            throw new Error('Generated asset file list differs. Run npm run build.');
        }
        for (const file of expectedFiles) {
            if (await digest(path.join(temporaryRoot, file)) !== await digest(path.join(assetsRoot, file))) {
                throw new Error(`Generated asset differs: ${file}`);
            }
        }
        await assertSourceMapsExist(assetsRoot);
        console.log(`Assets are complete and reproducible: ${actualFiles.length} files.`);
    } finally {
        await rm(temporaryRoot, {recursive: true, force: true});
    }
}

if (process.argv.includes('--check')) {
    await verifyAssets();
} else {
    await copyFiles(assetsRoot);
    await assertSourceMapsExist(assetsRoot);
    console.log(`Built ${(await listFiles(assetsRoot)).length} frontend asset files.`);
}
