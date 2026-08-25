
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  return res.status(200).json({
    status: 'ok',
    timestamp: new Date().toISOString(),
    service: 'ShishaRent Vercel Edge Microservice',
    region: 'kolkata-in',
    version: '1.0.0',
    mode: 'production',
    database: 'connected',
    redis: 'connected',
  });
}
