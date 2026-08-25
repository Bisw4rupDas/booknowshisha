
export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');
  return res.status(200).json({
    success: true,
    total: 23,
    categories: ['Fruit & Citrus', 'Mint & Ice Chill', 'Sweet & Confectionery', 'Spiced, Paan & Reserve Blends'],
    flavours: [
      { id: 1, name: 'SR Blueberry Blast', price: 600, category: 'Fruit & Citrus', shortDesc: 'Wild Blueberry • Crystal Ice • Signature SR Blend', sku: 'BMS-FLV-001', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 10.59.25 AM (2).jpeg' },
      { id: 2, name: 'SR Brainfreezer Gum Rose', price: 600, category: 'Mint & Ice Chill', shortDesc: 'Sub-Zero Menthol • Damask Rose • Sweet Spearmint Gum', sku: 'BMS-FLV-002', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM (1).jpeg' },
      { id: 3, name: 'SR Candy Crush', price: 600, category: 'Sweet & Confectionery', shortDesc: 'Candied Fruit Drops • Sweet Berries • Rainbow Confection', sku: 'BMS-FLV-003', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (1).jpeg' },
      { id: 4, name: 'SR Chief Commissioner', price: 650, category: 'Spiced, Paan & Reserve Blends', shortDesc: 'Royal Silver Vark • Kashmiri Saffron • Aged Royal Paan', sku: 'BMS-FLV-004', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.17 AM (2).jpeg' },
      { id: 5, name: 'SR Cola Blast', price: 600, category: 'Sweet & Confectionery', shortDesc: 'Effervescent Cola Syrup • Crushed Ice • Zesty Lime Fizz', sku: 'BMS-FLV-005', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM.jpeg' },
      { id: 6, name: 'SR Double Apple', price: 600, category: 'Traditional & Classic', shortDesc: 'Sweet Red Apple • Sour Green Apple • Anise Star Aroma', sku: 'BMS-FLV-006', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM (2).jpeg' },
      { id: 7, name: 'SR Fresh Mint', price: 600, category: 'Mint & Ice Chill', shortDesc: 'Crisp Garden Mint Leaves • Cooling Vapor Breeze', sku: 'BMS-FLV-007', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM.jpeg' },
      { id: 8, name: 'SR Grape Frost', price: 600, category: 'Fruit & Citrus', shortDesc: 'Dark Concord Grapes • Frosted Ice Crystals • Velvet Smoke', sku: 'BMS-FLV-008', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (2).jpeg' },
      { id: 9, name: 'SR Gum Cinnamon', price: 600, category: 'Spiced, Paan & Reserve Blends', shortDesc: 'Ceylon Sweet Cinnamon • Spiced Spearmint • Warm Molasses', sku: 'BMS-FLV-009', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM (1).jpeg' },
      { id: 10, name: 'SR Ice Kiwi', price: 600, category: 'Fruit & Citrus', shortDesc: 'Tart Emerald Kiwi • Chilled Ice Blast • Smooth Draw', sku: 'BMS-FLV-010', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (1).jpeg' },
      { id: 11, name: 'SR Ice Watermelon', price: 600, category: 'Fruit & Citrus', shortDesc: 'Summer Ripe Watermelon • Arctic Frost • Dense Clouds', sku: 'BMS-FLV-011', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 10.59.25 AM (2).jpeg' },
      { id: 12, name: 'SR Kiwi Blast', price: 600, category: 'Fruit & Citrus', shortDesc: 'Sweet Tropical Kiwi • Zesty Citrus Undertone', sku: 'BMS-FLV-012', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (1).jpeg' },
      { id: 13, name: 'SR Lemon Tea Mint', price: 600, category: 'Fruit & Citrus', shortDesc: 'Darjeeling Black Tea Leaf • Sun-ripened Lemon • Fresh Mint', sku: 'BMS-FLV-013', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM.jpeg' },
      { id: 14, name: 'SR Love 66', price: 600, category: 'Fruit & Citrus', shortDesc: 'Honeydew Melon • Passion Fruit • Icy Peppermint Cloud', sku: 'BMS-FLV-014', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 10.59.25 AM (2).jpeg' },
      { id: 15, name: 'SR Magical Night', price: 600, category: 'Sweet & Confectionery', shortDesc: 'Night-blooming Jasmine • Vanilla Cream • Blackberry Mystique', sku: 'BMS-FLV-015', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (1).jpeg' },
      { id: 16, name: 'SR Orange Blossom', price: 600, category: 'Fruit & Citrus', shortDesc: 'Nagpur Mandarin Orange • Sweet Citrus Floral Zest', sku: 'BMS-FLV-016', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (2).jpeg' },
      { id: 17, name: 'SR Paan Raas King', price: 600, category: 'Spiced, Paan & Reserve Blends', shortDesc: 'Authentic Calcutta Meetha Paan • Gulkand • Menthol Surge', sku: 'BMS-FLV-017', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.17 AM (2).jpeg' },
      { id: 18, name: 'SR Peach Twist', price: 600, category: 'Fruit & Citrus', shortDesc: 'Golden Velvet Peach • Tangy Apricot • Silky Cool Finish', sku: 'BMS-FLV-018', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM.jpeg' },
      { id: 19, name: 'SR Red Bull Ice', price: 600, category: 'Sweet & Confectionery', shortDesc: 'Electrifying Energy Drink Aroma • Tangy Taurine • Sub-Zero Frost', sku: 'BMS-FLV-019', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (1).jpeg' },
      { id: 20, name: 'SR Rooh Afza Royal Rose', price: 600, category: 'Spiced, Paan & Reserve Blends', shortDesc: 'Regal Rose Petal Syrup • Herbal Essence • Mughal Heritage Smoke', sku: 'BMS-FLV-020', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM (1).jpeg' },
      { id: 21, name: 'SR Spring Water', price: 600, category: 'Mint & Ice Chill', shortDesc: 'Pure Mountain Spring Vapor • Crisp Ozone • Clean Freshness', sku: 'BMS-FLV-021', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.19 AM.jpeg' },
      { id: 22, name: 'SR Strawberry Blast', price: 600, category: 'Fruit & Citrus', shortDesc: 'Fresh Mahabaleshwar Strawberry • Sweet Berry Glaze', sku: 'BMS-FLV-022', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 10.59.25 AM (2).jpeg' },
      { id: 23, name: 'SR Sweet Melon', price: 600, category: 'Fruit & Citrus', shortDesc: 'Cantaloupe • Honeyed Honeydew Nectar • Velvety Sweet Finish', sku: 'BMS-FLV-023', image: 'assets/images/gallery/WhatsApp Image 2026-08-23 at 11.44.18 AM (2).jpeg' },
    ]
  });
}
