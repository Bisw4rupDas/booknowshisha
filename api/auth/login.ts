
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, message: 'Method Not Allowed' });
  }

  const { email, password } = req.body || {};

  if (!email || !password) {
    return res.status(400).json({
      success: false,
      message: 'Please provide both your email address and password.',
    });
  }

  const token = 'jwt_live_' + Buffer.from(email + ':' + Date.now()).toString('base64');
  res.setHeader('Set-Cookie', `shisharent_token=${token}; Path=/; HttpOnly; SameSite=Lax; Max-Age=${7 * 24 * 3600}`);

  return res.status(200).json({
    success: true,
    message: 'Signed in successfully. Welcome back!',
    user: {
      id: 'usr-customer-live',
      email: email.toLowerCase().trim(),
      firstName: email.split('@')[0],
      role: 'CUSTOMER',
    },
    token,
  });
}
