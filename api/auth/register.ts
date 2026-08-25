
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, message: 'Method Not Allowed' });
  }

  const { email, password, firstName, lastName, phone } = req.body || {};

  if (!email || !email.includes('@') || !email.includes('.')) {
    return res.status(400).json({
      success: false,
      message: 'Please provide a valid email address.',
    });
  }

  if (!password || password.length < 6) {
    return res.status(400).json({
      success: false,
      message: 'Password must be at least 6 characters long.',
    });
  }

  // Simulated / Cloud User creation
  const userId = 'usr-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
  const token = 'jwt_live_' + Buffer.from(email + ':' + Date.now()).toString('base64');

  // Set HTTP-only Cookie
  res.setHeader('Set-Cookie', `shisharent_token=${token}; Path=/; HttpOnly; SameSite=Lax; Max-Age=${7 * 24 * 3600}`);

  return res.status(201).json({
    success: true,
    message: 'Account created successfully! Welcome to ShishaRent.',
    user: {
      id: userId,
      email: email.toLowerCase().trim(),
      firstName: firstName || email.split('@')[0],
      lastName: lastName || '',
      phone: phone || '',
      role: 'CUSTOMER',
    },
    token,
  });
}
