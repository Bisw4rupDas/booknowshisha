
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  return res.status(200).json({
    success: true,
    authenticated: true,
    user: {
      email: 'customer@shisharent.com',
      firstName: 'Rahul',
      lastName: 'Sen',
      role: 'CUSTOMER',
      city: 'Kolkata',
    }
  });
}
