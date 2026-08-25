
const KOLKATA_DISTRICTS: Record<string, { zoneName: string; district: string; fee: number }> = {
  // Central Kolkata Hub
  '700001': { zoneName: 'Central Kolkata Hub', district: 'Kolkata', fee: 150 },
  '700012': { zoneName: 'Central Kolkata Hub', district: 'Kolkata', fee: 150 },
  '700016': { zoneName: 'Park Street & Camac St', district: 'Kolkata', fee: 150 },
  '700017': { zoneName: 'Park Circus & Ballygunge', district: 'Kolkata', fee: 150 },
  '700069': { zoneName: 'Central Kolkata Hub', district: 'Kolkata', fee: 150 },
  '700071': { zoneName: 'Park Street Core', district: 'Kolkata', fee: 150 },
  
  // South Kolkata Hub
  '700019': { zoneName: 'Ballygunge & Gariahat', district: 'Kolkata', fee: 150 },
  '700025': { zoneName: 'Bhowanipore & Kalighat', district: 'Kolkata', fee: 150 },
  '700026': { zoneName: 'Lake Gardens & Kalighat', district: 'Kolkata', fee: 150 },
  '700027': { zoneName: 'Alipore & New Alipore', district: 'Kolkata', fee: 150 },
  '700029': { zoneName: 'Southern Avenue & Lake', district: 'Kolkata', fee: 150 },
  '700033': { zoneName: 'Tollygunge & Golf Green', district: 'Kolkata', fee: 150 },
  '700068': { zoneName: 'Jadavpur & Santoshpur', district: 'Kolkata', fee: 150 },
  '700084': { zoneName: 'Garia & Highlands', district: 'Kolkata', fee: 150 },

  // Salt Lake & New Town Hub (North 24 Pgs)
  '700064': { zoneName: 'Salt Lake Sector I & II', district: 'North 24 Parganas', fee: 150 },
  '700091': { zoneName: 'Salt Lake Sector V Tech Park', district: 'North 24 Parganas', fee: 150 },
  '700097': { zoneName: 'Salt Lake Sector III', district: 'North 24 Parganas', fee: 150 },
  '700098': { zoneName: 'Salt Lake Stadium Area', district: 'North 24 Parganas', fee: 150 },
  '700106': { zoneName: 'Salt Lake Central', district: 'North 24 Parganas', fee: 150 },
  '700156': { zoneName: 'New Town Action Area I', district: 'North 24 Parganas', fee: 150 },
  '700160': { zoneName: 'New Town Action Area II & III', district: 'North 24 Parganas', fee: 150 },

  // North Hub
  '700048': { zoneName: 'Lake Town & Bangur', district: 'North 24 Parganas', fee: 200 },
  '700055': { zoneName: 'Dum Dum & Airport Hub', district: 'North 24 Parganas', fee: 200 },
  '700089': { zoneName: 'Kankurgachi & Phoolbagan', district: 'Kolkata', fee: 150 },
  '700135': { zoneName: 'Rajarhat Main', district: 'North 24 Parganas', fee: 200 },
  '700136': { zoneName: 'Rajarhat Expressway', district: 'North 24 Parganas', fee: 200 },
};

export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  const pincode = String(req.query?.pincode || req.body?.postalCode || req.body?.pincode || '').trim();

  if (!pincode || pincode.length !== 6 || !/^\d{6}$/.test(pincode)) {
    return res.status(400).json({
      success: false,
      serviceable: false,
      message: 'Please enter a valid 6-digit Indian Postal PIN code.',
    });
  }

  // Check if starts with Kolkata postal network 700xxx
  const isKolkataNetwork = pincode.startsWith('700');
  const zoneMatch = KOLKATA_DISTRICTS[pincode];

  if (zoneMatch) {
    return res.status(200).json({
      success: true,
      serviceable: true,
      postalCode: pincode,
      zoneName: zoneMatch.zoneName,
      district: zoneMatch.district,
      city: 'Kolkata',
      deliveryFee: zoneMatch.fee,
      estimatedDelivery: '60-90 minutes',
      slots: [
        { id: 'slot-1', startTime: '14:00', endTime: '16:00', label: '14:00 - 16:00 (Afternoon Prime)' },
        { id: 'slot-2', startTime: '16:00', endTime: '18:00', label: '16:00 - 18:00 (Evening Sunset)' },
        { id: 'slot-3', startTime: '18:00', endTime: '20:00', label: '18:00 - 20:00 (Night Chill)' },
        { id: 'slot-4', startTime: '20:00', endTime: '22:00', label: '20:00 - 22:00 (Late Night Party)' },
      ],
      message: `PIN ${pincode} (${zoneMatch.zoneName}, ${zoneMatch.district}) is eligible for 60-90 min Express Hookah delivery.`,
    });
  }

  if (isKolkataNetwork) {
    return res.status(200).json({
      success: true,
      serviceable: true,
      postalCode: pincode,
      zoneName: 'Greater Kolkata Service Area',
      district: 'Kolkata Metropolitan Area',
      city: 'Kolkata',
      deliveryFee: 150,
      estimatedDelivery: '90 minutes',
      slots: [
        { id: 'slot-1', startTime: '14:00', endTime: '16:00', label: '14:00 - 16:00' },
        { id: 'slot-2', startTime: '16:00', endTime: '18:00', label: '16:00 - 18:00' },
        { id: 'slot-3', startTime: '18:00', endTime: '20:00', label: '18:00 - 20:00' },
        { id: 'slot-4', startTime: '20:00', endTime: '22:00', label: '20:00 - 22:00' },
      ],
      message: `PIN ${pincode} (Greater Kolkata) is eligible for ShishaRent on-demand delivery.`,
    });
  }

  return res.status(200).json({
    success: false,
    serviceable: false,
    postalCode: pincode,
    message: 'Delivery is currently available exclusively in Kolkata, North 24 Parganas and South 24 Parganas.',
  });
}
