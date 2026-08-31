import { PrismaClient, UserRole, HookahCondition, HookahInventoryStatus } from '@prisma/client';
import * as bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Starting ShishaRent database seeding...');

  // --------------------------------------------------------------------------
  // 1. IDENTITY & USERS
  // --------------------------------------------------------------------------
  const defaultPassword = await bcrypt.hash('ShishaRent@2026', 10);

  // Admin User
  const adminUser = await prisma.user.upsert({
    where: { email: 'admin@shisharent.com' },
    update: {},
    create: {
      email: 'admin@shisharent.com',
      passwordHash: defaultPassword,
      role: UserRole.SUPER_ADMIN,
      isVerified: true,
      admin: {
        create: {
          fullName: 'Executive Administrator',
          department: 'Management',
        },
      },
    },
  });

  // Staff User (Delivery Lead)
  const staffUser = await prisma.user.upsert({
    where: { email: 'staff@shisharent.com' },
    update: {},
    create: {
      email: 'staff@shisharent.com',
      passwordHash: defaultPassword,
      role: UserRole.STAFF,
      isVerified: true,
      staff: {
        create: {
          fullName: 'Vikram Singh',
          phone: '+919811002233',
          designation: 'Senior Delivery & Inspection Specialist',
        },
      },
    },
  });

  // Customer User
  const customerUser = await prisma.user.upsert({
    where: { email: 'customer@shisharent.com' },
    update: {},
    create: {
      email: 'customer@shisharent.com',
      passwordHash: defaultPassword,
      role: UserRole.CUSTOMER,
      isVerified: true,
      customer: {
        create: {
          firstName: 'Rahul',
          lastName: 'Sen',
          phone: '+919903556825',
          addressLine1: '42, Salt Lake Sector V',
          city: 'Kolkata',
          postalCode: '700091',
        },
      },
    },
  });

  console.log('✓ Users & Roles seeded (Admin, Staff, Customer)');

  // --------------------------------------------------------------------------
  // 2. DELIVERY ZONES & SLOTS (KOLKATA SERVICE NETWORK)
  // --------------------------------------------------------------------------
  const zonesData = [
    {
      name: 'Salt Lake & New Town',
      postalCodes: ['700064', '700091', '700097', '700098', '700106', '700156', '700160'],
      baseFee: 150.0,
      slots: [
        { startTime: '14:00', endTime: '16:00', maxCapacity: 6 },
        { startTime: '16:00', endTime: '18:00', maxCapacity: 6 },
        { startTime: '18:00', endTime: '20:00', maxCapacity: 8 },
        { startTime: '20:00', endTime: '22:00', maxCapacity: 8 },
      ],
    },
    {
      name: 'Central Kolkata',
      postalCodes: ['700001', '700012', '700016', '700017', '700069', '700071'],
      baseFee: 150.0,
      slots: [
        { startTime: '14:00', endTime: '16:00', maxCapacity: 6 },
        { startTime: '16:00', endTime: '18:00', maxCapacity: 6 },
        { startTime: '18:00', endTime: '20:00', maxCapacity: 8 },
        { startTime: '20:00', endTime: '22:00', maxCapacity: 8 },
      ],
    },
    {
      name: 'South Kolkata',
      postalCodes: ['700019', '700025', '700026', '700027', '700029', '700033', '700068', '700084'],
      baseFee: 150.0,
      slots: [
        { startTime: '14:00', endTime: '16:00', maxCapacity: 5 },
        { startTime: '16:00', endTime: '18:00', maxCapacity: 5 },
        { startTime: '18:00', endTime: '20:00', maxCapacity: 7 },
        { startTime: '20:00', endTime: '22:00', maxCapacity: 7 },
      ],
    },
    {
      name: 'Rajarhat & North Hubs',
      postalCodes: ['700048', '700055', '700089', '700135', '700136'],
      baseFee: 200.0,
      slots: [
        { startTime: '14:00', endTime: '16:00', maxCapacity: 5 },
        { startTime: '16:00', endTime: '18:00', maxCapacity: 5 },
        { startTime: '18:00', endTime: '20:00', maxCapacity: 6 },
        { startTime: '20:00', endTime: '22:00', maxCapacity: 6 },
      ],
    },
  ];

  for (const zoneInfo of zonesData) {
    const existingZone = await prisma.deliveryZone.findFirst({
      where: { name: zoneInfo.name },
    });

    const zone = existingZone
      ? existingZone
      : await prisma.deliveryZone.create({
          data: {
            name: zoneInfo.name,
            baseFee: zoneInfo.baseFee,
          },
        });

    for (const pin of zoneInfo.postalCodes) {
      const existingPin = await prisma.deliveryPostalCode.findFirst({
        where: { zoneId: zone.id, postalCode: pin },
      });
      if (!existingPin) {
        await prisma.deliveryPostalCode.create({
          data: {
            zoneId: zone.id,
            postalCode: pin,
          },
        });
      }
    }

    for (const slot of zoneInfo.slots) {
      const existingSlot = await prisma.deliverySlot.findFirst({
        where: {
          zoneId: zone.id,
          startTime: slot.startTime,
          endTime: slot.endTime,
        },
      });

      if (!existingSlot) {
        await prisma.deliverySlot.create({
          data: {
            zoneId: zone.id,
            startTime: slot.startTime,
            endTime: slot.endTime,
            maxCapacity: slot.maxCapacity,
          },
        });
      }
    }
  }

  console.log('✓ Delivery Zones & Time Slots seeded');

  // --------------------------------------------------------------------------
  // 3. HOOKAH CATALOG & PHYSICAL INVENTORY
  // --------------------------------------------------------------------------
  const hookahModelsData = [
    {
      name: 'Khalil Mamoon Gold Classic',
      slug: 'km-gold-classic',
      description: 'Authentic Egyptian brass handcrafted hookah with ornate gold accents and deep rumbling draw.',
      heightCm: 78.0,
      material: 'Handcrafted Egyptian Brass & Bohemian Glass',
      basePrice: 999.0,
      depositFee: 1500.0,
      imageUrl: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
      unitsCount: 6,
      prefix: 'KM-GLD',
    },
    {
      name: 'Oduman Glass Modern N2 Travel',
      slug: 'oduman-n2-travel',
      description: 'Compact ultra-pure Turkish borosilicate glass hookah with integrated LED light base and silicone hose.',
      heightCm: 28.0,
      material: 'High-Grade Borosilicate Glass & Stainless Steel',
      basePrice: 1299.0,
      depositFee: 2000.0,
      imageUrl: 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?auto=format&fit=crop&w=800&q=80',
      unitsCount: 5,
      prefix: 'ODU-N2',
    },
    {
      name: 'Starbuzz Carbine Matte Stealth',
      slug: 'starbuzz-carbine-matte',
      description: 'Tactical all-terrain hookah with 4-point all-terrain stabilizing legs, 360-degree rotating hose stem, and matte anodized finish.',
      heightCm: 72.0,
      material: 'Aerospace Anodized Aluminum & V2A Steel',
      basePrice: 1499.0,
      depositFee: 2500.0,
      imageUrl: 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80',
      unitsCount: 4,
      prefix: 'SB-CARB',
    },
    {
      name: 'Amy Deluxe Stainless Steel Heavy',
      slug: 'amy-deluxe-ss',
      description: 'German engineered click-system hookah delivering effortless airtight cloud density and modern purge valve system.',
      heightCm: 65.0,
      material: 'Heavy Brushed Stainless Steel',
      basePrice: 1199.0,
      depositFee: 1800.0,
      imageUrl: 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=800&q=80',
      unitsCount: 5,
      prefix: 'AMY-SS',
    },
  ];

  for (const item of hookahModelsData) {
    const model = await prisma.hookahModel.upsert({
      where: { slug: item.slug },
      update: {
        basePrice: item.basePrice,
        depositFee: item.depositFee,
      },
      create: {
        name: item.name,
        slug: item.slug,
        description: item.description,
        heightCm: item.heightCm,
        material: item.material,
        basePrice: item.basePrice,
        depositFee: item.depositFee,
        imageUrl: item.imageUrl,
      },
    });

    // Seed physical inventory units
    for (let i = 1; i <= item.unitsCount; i++) {
      const serial = `${item.prefix}-${String(i).padStart(3, '0')}`;
      await prisma.hookahInventory.upsert({
        where: { serialNumber: serial },
        update: {},
        create: {
          hookahModelId: model.id,
          serialNumber: serial,
          barcode: `BAR-${serial}`,
          condition: HookahCondition.EXCELLENT,
          status: HookahInventoryStatus.AVAILABLE,
          notes: 'Factory fresh inventory unit',
        },
      });
    }
  }

  console.log('✓ Hookah Models & Physical Serialized Units seeded');

  // --------------------------------------------------------------------------
  // 4. FLAVOUR CATEGORIES & FLAVOURS
  // --------------------------------------------------------------------------
  const flavourData = [
    {
      category: 'Fruity & Sweet',
      slug: 'fruity-sweet',
      description: 'Lush tropical, berry, and citrus blends',
      flavours: [
        { name: 'Blueberry Mint Ice', slug: 'blueberry-mint', brand: 'Al Fakher', isNicotine: true, qty: 80 },
        { name: 'Love 66 (Passionfruit Melon Mint)', slug: 'love-66', brand: 'Adalya', isNicotine: true, qty: 75 },
        { name: 'Lady Killer (Peach Mango Mint)', slug: 'lady-killer', brand: 'Adalya', isNicotine: true, qty: 60 },
        { name: 'Watermelon Freeze', slug: 'watermelon-freeze', brand: 'Starbuzz', isNicotine: true, qty: 50 },
      ],
    },
    {
      category: 'Traditional & Classic',
      slug: 'traditional-classic',
      description: 'Timeless Middle Eastern recipe staples',
      flavours: [
        { name: 'Double Apple Two Apples', slug: 'double-apple', brand: 'Al Fakher', isNicotine: true, qty: 100 },
        { name: 'Grape Mint Signature', slug: 'grape-mint', brand: 'Al Fakher', isNicotine: true, qty: 90 },
        { name: 'Pure Mint Absolute', slug: 'pure-mint', brand: 'Al Fakher', isNicotine: true, qty: 85 },
      ],
    },
    {
      category: 'Exotic & Indian Fusion',
      slug: 'exotic-indian-fusion',
      description: 'Aromatic betel leaf, spices, and rose essences',
      flavours: [
        { name: 'Paan Raas King', slug: 'paan-raas', brand: 'Afzal', isNicotine: true, qty: 120 },
        { name: 'Bombay Pan Masala', slug: 'bombay-pan-masala', brand: 'Afzal', isNicotine: true, qty: 65 },
        { name: 'Rooh Afza Royal Rose', slug: 'rooh-afza-rose', brand: 'Afzal', isNicotine: true, qty: 55 },
      ],
    },
    {
      category: 'Herbal & 0% Tobacco',
      slug: 'herbal-0-percent-tobacco',
      description: '100% tobacco-free and nicotine-free herbal cane molasses',
      flavours: [
        { name: 'Herbal Citrus Lemon Blast', slug: 'herbal-citrus-burst', brand: 'Hydro', isNicotine: false, qty: 45 },
        { name: 'Herbal Blue Mist Cloud', slug: 'herbal-blue-mist', brand: 'Hydro', isNicotine: false, qty: 40 },
      ],
    },
  ];

  for (const cat of flavourData) {
    const category = await prisma.flavourCategory.upsert({
      where: { slug: cat.slug },
      update: {},
      create: {
        name: cat.category,
        slug: cat.slug,
        description: cat.description,
      },
    });

    for (const f of cat.flavours) {
      const flavour = await prisma.flavour.upsert({
        where: { slug: f.slug },
        update: {},
        create: {
          categoryId: category.id,
          name: f.name,
          slug: f.slug,
          brand: f.brand,
          isNicotine: f.isNicotine,
          stock: {
            create: {
              quantityUnits: f.qty,
              lowStockAlert: 15,
            },
          },
        },
      });
    }
  }

  console.log('✓ Flavour Categories, Flavours & Inventory Stock seeded');

  // --------------------------------------------------------------------------
  // 5. PACKAGES & CONFIGURATION
  // --------------------------------------------------------------------------
  const kmModel = await prisma.hookahModel.findUnique({ where: { slug: 'km-gold-classic' } });
  const odumanModel = await prisma.hookahModel.findUnique({ where: { slug: 'oduman-n2-travel' } });
  const starbuzzModel = await prisma.hookahModel.findUnique({ where: { slug: 'starbuzz-carbine-matte' } });

  const packagesData = [
    {
      name: 'Solo Standard 24H Package',
      slug: 'solo-standard-24h',
      description: 'Perfect for an evening chill or solo session. Includes 1 Hookah, 2 Flavour Heads, Coconut Charcoal & Hygienic Mouthpieces.',
      price: 1499.0,
      durationHrs: 24,
      maxFlavours: 2,
      includesCoals: true,
      includesMouthpieces: 4,
      hookahModelId: kmModel?.id,
    },
    {
      name: 'Duo Weekend 48H Package',
      slug: 'duo-weekend-48h',
      description: 'Ideal for weekend getaways and small gatherings. Includes 1 Premium Hookah, 4 Flavour Heads, Extended Coal Pack & 8 Mouthpieces.',
      price: 2499.0,
      durationHrs: 48,
      maxFlavours: 4,
      includesCoals: true,
      includesMouthpieces: 8,
      hookahModelId: odumanModel?.id,
    },
    {
      name: 'VIP Party Celebration 72H Package',
      slug: 'vip-party-72h',
      description: 'The ultimate luxury experience for parties. Includes Top-tier Stealth Hookah, 6 Curated Flavour Heads, Heavy-duty Coal Burner, XL Charcoal & 12 Mouthpieces.',
      price: 3499.0,
      durationHrs: 72,
      maxFlavours: 6,
      includesCoals: true,
      includesMouthpieces: 12,
      hookahModelId: starbuzzModel?.id,
    },
  ];

  for (const pkg of packagesData) {
    const createdPkg = await prisma.package.upsert({
      where: { slug: pkg.slug },
      update: {
        price: pkg.price,
        durationHrs: pkg.durationHrs,
        maxFlavours: pkg.maxFlavours,
      },
      create: {
        name: pkg.name,
        slug: pkg.slug,
        description: pkg.description,
        price: pkg.price,
        durationHrs: pkg.durationHrs,
        maxFlavours: pkg.maxFlavours,
        includesCoals: pkg.includesCoals,
        includesMouthpieces: pkg.includesMouthpieces,
      },
    });

    if (pkg.hookahModelId) {
      const existingItem = await prisma.packageItem.findFirst({
        where: { packageId: createdPkg.id, hookahModelId: pkg.hookahModelId },
      });
      if (!existingItem) {
        await prisma.packageItem.create({
          data: {
            packageId: createdPkg.id,
            hookahModelId: pkg.hookahModelId,
            quantity: 1,
          },
        });
      }
    }
  }

  console.log('✓ Rental Packages & Tier Bundles seeded');
  console.log('✨ ShishaRent Database Seeding Completed Successfully!');
}

main()
  .catch((e) => {
    console.error('❌ Error during seeding:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
