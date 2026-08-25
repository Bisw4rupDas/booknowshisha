
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  return res.status(200).json({
    success: true,
    message: 'Thank you for reaching out! Our Kolkata concierge team has received your message and will respond within 30 minutes.',
  });
}
