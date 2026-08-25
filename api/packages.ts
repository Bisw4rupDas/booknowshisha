
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  return res.status(200).json({
    success: true,
    packages: [
      {
        id: 'pkg-solo-24h',
        name: 'Solo Standard 24H Package',
        slug: 'solo-standard-24h',
        description: 'Perfect for an evening chill or solo session. Includes 1 Hookah, 2 Flavour Heads, Coconut Charcoal & Hygienic Mouthpieces.',
        specs: '1x Classic Clay Bowl • Coconut Coals • 60-Min Session',
        price: 1499.00,
        durationHrs: 24,
        maxFlavours: 2,
        chillumIncluded: 'Classic Clay',
        depositFee: 1500.00,
        image: 'rentals/sr-basic-hookah.png',
      },
      {
        id: 'pkg-duo-48h',
        name: 'Duo Weekend 48H Package',
        slug: 'duo-weekend-48h',
        description: 'Ideal for weekend getaways and small gatherings. Includes 1 Premium Hookah, 4 Flavour Heads, Extended Coal Pack & 8 Mouthpieces.',
        specs: 'Dense Phunnel Pack • Extended Coals • 2x 60-Min Sessions',
        price: 2499.00,
        durationHrs: 48,
        maxFlavours: 4,
        chillumIncluded: 'Classic Clay',
        depositFee: 2000.00,
        image: 'rentals/sr-regular-hookah.png',
      },
      {
        id: 'pkg-vip-72h',
        name: 'VIP Party Celebration 72H Package',
        slug: 'vip-party-72h',
        description: 'The ultimate luxury experience for parties. Includes Top-tier Stealth Hookah, 6 Curated Flavour Heads, Heavy-duty Coal Burner, XL Charcoal & 12 Mouthpieces.',
        specs: 'Reserve Saffron Blend • Master Mixology • Unlimited Session Coals',
        price: 3499.00,
        durationHrs: 72,
        maxFlavours: 6,
        chillumIncluded: 'Classic Clay',
        depositFee: 2500.00,
        image: 'rentals/sr-priyam-hookah.png',
      },
    ]
  });
}
