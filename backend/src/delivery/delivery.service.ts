import { Injectable, NotFoundException, BadRequestException, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CheckZoneDto } from './dto/check-zone.dto';
import { PinServiceabilityService } from './serviceability/pin-serviceability.service';

@Injectable()
export class DeliveryService {
  private readonly logger = new Logger(DeliveryService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly pinServiceability: PinServiceabilityService,
  ) {}

  /**
   * Single Source of Truth: Check if a postal PIN is serviceable
   * within Kolkata, North 24 Parganas, or South 24 Parganas.
   */
  async checkZone(dto: CheckZoneDto) {
    const serviceability = this.pinServiceability.checkPinServiceability(dto.postalCode);

    if (!serviceability.deliverable) {
      return {
        serviceable: false,
        deliverable: false,
        postalCode: dto.postalCode,
        pin: dto.postalCode,
        district: serviceability.district,
        state: serviceability.state,
        area: serviceability.area,
        allowedDistricts: serviceability.allowedDistricts,
        message: serviceability.message,
      };
    }

    // Lookup available delivery zones and slots in database
    const zones = await this.prisma.deliveryZone.findMany({
      where: { isActive: true },
      include: {
        slots: {
          where: { isActive: true },
          orderBy: { startTime: 'asc' },
        },
      },
    });

    const matchedZone = zones.find((z) => z.postalCodes.includes(dto.postalCode)) || zones[0];

    const availableSlots = matchedZone?.slots?.map((s) => ({
      id: s.id,
      timeWindow: `${s.startTime} - ${s.endTime}`,
      startTime: s.startTime,
      endTime: s.endTime,
    })) || [
      { id: '14:00-16:00', timeWindow: '14:00 - 16:00', startTime: '14:00', endTime: '16:00' },
      { id: '16:00-18:00', timeWindow: '16:00 - 18:00', startTime: '16:00', endTime: '18:00' },
      { id: '18:00-20:00', timeWindow: '18:00 - 20:00', startTime: '18:00', endTime: '20:00' },
      { id: '20:00-22:00', timeWindow: '20:00 - 22:00', startTime: '20:00', endTime: '22:00' },
    ];

    return {
      serviceable: true,
      deliverable: true,
      postalCode: dto.postalCode,
      pin: dto.postalCode,
      district: serviceability.district,
      state: serviceability.state,
      area: serviceability.area,
      zoneName: matchedZone ? matchedZone.name : serviceability.zoneName,
      baseDeliveryFee: matchedZone ? Number(matchedZone.baseFee) : (serviceability.baseDeliveryFee || 150),
      availableSlots,
      allowedDistricts: serviceability.allowedDistricts,
      message: serviceability.message,
    };
  }

  async getZones() {
    return this.prisma.deliveryZone.findMany({
      where: { isActive: true },
      include: {
        slots: {
          where: { isActive: true },
          orderBy: { startTime: 'asc' },
        },
      },
      orderBy: { name: 'asc' },
    });
  }

  async getSlots(postalCode?: string) {
    if (postalCode) {
      const serviceability = this.pinServiceability.checkPinServiceability(postalCode);
      if (!serviceability.deliverable) {
        throw new BadRequestException(serviceability.message);
      }

      const zone = await this.prisma.deliveryZone.findFirst({
        where: {
          isActive: true,
          postalCodes: { has: postalCode },
        },
        include: {
          slots: {
            where: { isActive: true },
            orderBy: { startTime: 'asc' },
          },
        },
      });

      if (!zone) {
        // Return default slots if specific zone record is not yet created
        const allSlots = await this.prisma.deliverySlot.findMany({
          where: { isActive: true },
          include: { zone: true },
          orderBy: { startTime: 'asc' },
        });

        return {
          district: serviceability.district,
          state: serviceability.state,
          zone: serviceability.zoneName || `${serviceability.district} Fleet`,
          baseFee: serviceability.baseDeliveryFee || 150,
          slots: allSlots.length > 0 ? allSlots : [
            { id: '14:00-16:00', startTime: '14:00', endTime: '16:00', maxCapacity: 6, isActive: true },
            { id: '16:00-18:00', startTime: '16:00', endTime: '18:00', maxCapacity: 6, isActive: true },
            { id: '18:00-20:00', startTime: '18:00', endTime: '20:00', maxCapacity: 8, isActive: true },
            { id: '20:00-22:00', startTime: '20:00', endTime: '22:00', maxCapacity: 8, isActive: true },
          ],
        };
      }

      return {
        district: serviceability.district,
        state: serviceability.state,
        zone: zone.name,
        baseFee: Number(zone.baseFee),
        slots: zone.slots,
      };
    }

    return this.prisma.deliverySlot.findMany({
      where: { isActive: true },
      include: { zone: true },
      orderBy: { startTime: 'asc' },
    });
  }
}
