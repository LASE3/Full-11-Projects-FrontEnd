const fs = require('fs');
const path = require('path');
const https = require('https');

const FONTS_DIR = path.join(__dirname, '..', 'assets', 'fonts');
if (!fs.existsSync(FONTS_DIR)) {
  fs.mkdirSync(FONTS_DIR, { recursive: true });
}

function downloadFile(url, destPath) {
  return new Promise((resolve, reject) => {
    https.get(url, (res) => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
        return downloadFile(res.headers.location, destPath).then(resolve).catch(reject);
      }
      if (res.statusCode !== 200) {
        return reject(new Error(`Status ${res.statusCode} for ${url}`));
      }
      const stream = fs.createWriteStream(destPath);
      res.pipe(stream);
      stream.on('finish', () => stream.close(resolve));
      stream.on('error', reject);
    }).on('error', reject);
  });
}

function fetchText(url) {
  return new Promise((resolve, reject) => {
    https.get(url, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
      }
    }, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve(data));
      res.on('error', reject);
    }).on('error', reject);
  });
}

async function main() {
  console.log('Downloading fonts for offline operation...');

  // 1. IBM Plex Sans + JetBrains Mono
  const googleFontsCssUrl = 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap';
  const fontsCss = await fetchText(googleFontsCssUrl);

  // 2. Material Symbols Outlined
  const matSymbolsCssUrl = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0';
  const matSymbolsCss = await fetchText(matSymbolsCssUrl);

  const combinedCss = fontsCss + '\n' + matSymbolsCss;

  // Parse font urls from CSS: url(https://fonts.gstatic.com/s/...) format('woff2')
  const urlRegex = /url\((https:\/\/fonts\.gstatic\.com\/[^)]+)\)\s+format\('woff2'\)/g;
  let match;
  let fontIndex = 0;
  let localCss = combinedCss;

  const downloadedUrls = new Map();

  while ((match = urlRegex.exec(combinedCss)) !== null) {
    const fontUrl = match[1];
    if (!downloadedUrls.has(fontUrl)) {
      fontIndex++;
      const urlParts = fontUrl.split('/');
      const originalFileName = urlParts[urlParts.length - 1];
      const localFileName = `font-${fontIndex}-${originalFileName}`;
      const localFilePath = path.join(FONTS_DIR, localFileName);

      console.log(`Downloading [${fontIndex}] ${localFileName}...`);
      await downloadFile(fontUrl, localFilePath);
      downloadedUrls.set(fontUrl, `../fonts/${localFileName}`);
    }
  }

  // Replace all gstatic URLs with local paths
  for (const [remoteUrl, localRelPath] of downloadedUrls.entries()) {
    localCss = localCss.split(remoteUrl).join(localRelPath);
  }

  // Write assets/css/fonts.css
  const outCssPath = path.join(__dirname, '..', 'assets', 'css', 'fonts.css');
  fs.writeFileSync(outCssPath, localCss, 'utf8');
  console.log(`Generated self-hosted fonts stylesheet: ${outCssPath}`);
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
