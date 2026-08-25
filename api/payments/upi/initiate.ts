
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  const amount = Number(req.body?.amount || 1649.0);
  const bookingNumber = req.body?.bookingNumber || `SR-BK-${Math.floor(100000 + Math.random() * 900000)}`;
  const paymentNumber = `SR-PAY-${Math.floor(100000 + Math.random() * 900000)}`;
  const vpa = 'shisharent@icici';
  const payeeName = 'ShishaRent Kolkata';
  
  const upiIntent = `upi://pay?pa=${vpa}&pn=${encodeURIComponent(payeeName)}&am=${amount.toFixed(2)}&cu=INR&tr=${paymentNumber}&tn=${encodeURIComponent('ShishaRent Reservation ' + bookingNumber)}`;
  const qrData = upiIntent;

  return res.status(201).json({
    success: true,
    paymentNumber,
    bookingNumber,
    amount,
    currency: 'INR',
    vpa,
    payeeName,
    upiIntent,
    qrData,
    expiryMinutes: 15,
    instructions: 'Scan the UPI QR code or tap the intent link in your UPI app (GPay, PhonePe, Paytm) to complete payment.',
  });
}
