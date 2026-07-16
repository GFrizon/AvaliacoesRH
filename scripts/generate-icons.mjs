/**
 * Gera todos os favicons/ícones PWA a partir das fontes vetoriais em
 * resources/icon/*.svg. Execução única (ou sempre que o ícone-fonte
 * mudar) — os PNGs resultantes ficam versionados em public/.
 *
 * Uso: node scripts/generate-icons.mjs
 */
import sharp from 'sharp';
import pngToIco from 'png-to-ico';
import { mkdir, writeFile } from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');

const SOURCE = path.join(root, 'resources/icon/source.svg');
const SOURCE_MASKABLE = path.join(root, 'resources/icon/source-maskable.svg');
const OUT_ICONS = path.join(root, 'public/icons');
const OUT_PUBLIC = path.join(root, 'public');

async function renderPng(source, size, outPath) {
    await sharp(source, { density: 384 })
        .resize(size, size)
        .png()
        .toFile(outPath);
    console.log('generated', path.relative(root, outPath));
}

async function main() {
    await mkdir(OUT_ICONS, { recursive: true });

    const targets = [
        [SOURCE, 16, 'favicon-16x16.png'],
        [SOURCE, 32, 'favicon-32x32.png'],
        [SOURCE, 180, 'apple-touch-icon.png'],
        [SOURCE, 192, 'android-chrome-192x192.png'],
        [SOURCE, 512, 'android-chrome-512x512.png'],
        [SOURCE, 192, 'pwa-192x192.png'],
        [SOURCE, 512, 'pwa-512x512.png'],
        [SOURCE_MASKABLE, 192, 'maskable-icon-192x192.png'],
        [SOURCE_MASKABLE, 512, 'maskable-icon-512x512.png'],
    ];

    for (const [source, size, filename] of targets) {
        await renderPng(source, size, path.join(OUT_ICONS, filename));
    }

    // favicon.ico a partir dos PNGs 16/32
    const ico = await pngToIco([
        path.join(OUT_ICONS, 'favicon-16x16.png'),
        path.join(OUT_ICONS, 'favicon-32x32.png'),
    ]);
    await writeFile(path.join(OUT_PUBLIC, 'favicon.ico'), ico);
    console.log('generated', 'public/favicon.ico');
}

main().catch((error) => {
    console.error(error);
    process.exit(1);
});
