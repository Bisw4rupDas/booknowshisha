/**
 * ==============================================================================
 * BookNowShisha - Production Application Entrypoint for cPanel / Phusion Passenger
 * ==============================================================================
 * This startup file launches the compiled NestJS core API backend.
 * Compatible with:
 * - cPanel "Setup Node.js App"
 * - CloudLinux LVE Manager
 * - Phusion Passenger (PassengerNodejs)
 * - cPanel Application Manager
 * ==============================================================================
 */

const path = require('path');
const fs = require('fs');

// Ensure production environment defaults if not explicitly passed by cPanel
if (!process.env.NODE_ENV) {
  process.env.NODE_ENV = 'production';
}

// Candidates for compiled NestJS application entrypoint
const candidatePaths = [
  path.resolve(__dirname, 'backend', 'dist', 'main.js'),
  path.resolve(__dirname, 'dist', 'main.js'),
  path.resolve(__dirname, 'backend', 'dist', 'src', 'main.js'),
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
  console.error('================================================================');
  console.error('[cPanel Passenger Error] Compiled NestJS build was not found.');
  console.error('Please build the application before launching:');
  console.error('  cd backend && npm install && npm run build');
  console.error('================================================================');
  process.exit(1);
}
