
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, message: 'Method Not Allowed' });
  }

  const body = req.body || {};
  const bookingNumber = 'SR-BK-' + Math.floor(100000 + Math.random() * 900000);
  const rentalId = 'rnt-' + Date.now();
  const packagePrice = Number(body.packagePrice || 1499.0);
  const deliveryFee = 150.0;
  const depositFee = 1500.0;
  const totalAmount = packagePrice + deliveryFee;

  return res.status(201).json({
    success: true,
    booking: {
      id: 'bk-' + Date.now(),
      bookingNumber,
      rentalStart: body.rentalStart || new Date().toISOString(),
      durationHrs: body.durationHrs || 24,
      deliverySlotId: body.deliverySlotId || 'slot-3',
      deliveryAddress: body.deliveryAddress || 'Kolkata',
      postalCode: body.postalCode || '700019',
      status: 'CONFIRMED',
      chillumMaterial: body.chillumMaterial || 'Classic Clay',
      flavourIds: body.flavourIds || ['SR Blueberry Blast', 'SR Fresh Mint'],
    },
    rental: {
      id: rentalId,
      status: 'PREPARING',
    },
    breakdown: {
      packagePrice,
      deliveryFee,
      depositFee,
      totalToPay: totalAmount,
    },
    message: `Booking ${bookingNumber} reserved successfully.`,
  });
}
