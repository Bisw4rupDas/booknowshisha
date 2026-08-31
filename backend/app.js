/**
 * ==============================================================================
 * BookNowShisha - Production Application Entrypoint for cPanel / Phusion Passenger
 * (When cPanel Application Root is set directly to backend directory)
 * ==============================================================================
 */

const path = require('path');
const fs = require('fs');

if (!process.env.NODE_ENV) {
  process.env.NODE_ENV = 'production';
}

const candidatePaths = [
  path.resolve(__dirname, 'dist', 'main.js'),
  path.resolve(__dirname, 'dist', 'src', 'main.js'),
];

let entrypointFound = false;

for (const targetPath of candidatePaths) {
  if (fs.existsSync(targetPath)) {
    entrypointFound = true;
    console.log(`[cPanel Passenger Bootstrap] Starting BookNowShisha NestJS backend from: ${targetPath}`);
    require(targetPath);
    break;
  }
}

if (!entrypointFound) {
  console.error('[cPanel Passenger Error] Compiled NestJS build was not found.');
  console.error('Please build the application: npm run build');
  process.exit(1);
}
