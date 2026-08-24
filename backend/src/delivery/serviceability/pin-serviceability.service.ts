import { Injectable, Logger } from '@nestjs/common';
import {
  ALLOWED_DELIVERY_DISTRICTS,
  PIN_DIRECTORY,
  AllowedDeliveryDistrict,
} from './pin-serviceability.data';

export interface PinResolutionResult {
  pin: string;
  district: string;
  state: string;
  area: string;
}

export interface ServiceabilityResult {
  deliverable: boolean;
  pin: string;
  district: string | null;
  state: string | null;
  area?: string;
  allowedDistricts: readonly string[];
  message: string;
  zoneName?: string;
  baseDeliveryFee?: number;
  availableSlots?: Array<{
    id: string;
    timeWindow: string;
    startTime: string;
    endTime: string;
  }>;
}

@Injectable()
export class PinServiceabilityService {
  private readonly logger = new Logger(PinServiceabilityService.name);

  /**
   * The strict 3-district delivery whitelist
   */
  public readonly allowedDistricts = ALLOWED_DELIVERY_DISTRICTS;

  /**
   * Resolves a 6-digit PIN to its authoritative postal District, State, and Area Name.
   * If unknown or invalid, returns null.
   */
  public resolvePin(pin: string): PinResolutionResult | null {
    if (!pin || typeof pin !== 'string') {
      return null;
    }

    const cleanPin = pin.trim();
    if (!/^[1-9][0-9]{5}$/.test(cleanPin)) {
      return null;
    }

    // 1. Direct directory lookup
    if (PIN_DIRECTORY[cleanPin]) {
      const entry = PIN_DIRECTORY[cleanPin];
      return {
        pin: cleanPin,
        district: entry.district,
        state: entry.state,
        area: entry.area,
      };
    }

    // 2. Known Postal Division Prefixes for neighboring non-serviceable districts & regions
    // (Ensures explicit district mapping even for unlisted specific sub-offices)
    if (cleanPin.startsWith('711')) {
      return { pin: cleanPin, district: 'Howrah', state: 'West Bengal', area: 'Howrah Postal Division' };
    }
    if (cleanPin.startsWith('712')) {
      return { pin: cleanPin, district: 'Hooghly', state: 'West Bengal', area: 'Hooghly Postal Division' };
    }
    if (cleanPin.startsWith('741')) {
      return { pin: cleanPin, district: 'Nadia', state: 'West Bengal', area: 'Nadia Postal Division' };
    }
    if (cleanPin.startsWith('734')) {
      return { pin: cleanPin, district: 'Darjeeling', state: 'West Bengal', area: 'Darjeeling / Siliguri Division' };
    }
    if (cleanPin.startsWith('713')) {
      return { pin: cleanPin, district: 'Bardhaman', state: 'West Bengal', area: 'Bardhaman Division' };
    }
    if (cleanPin.startsWith('721')) {
      return { pin: cleanPin, district: 'Medinipur', state: 'West Bengal', area: 'Medinipur Division' };
    }
    if (cleanPin.startsWith('731')) {
      return { pin: cleanPin, district: 'Birbhum', state: 'West Bengal', area: 'Birbhum Division' };
    }
    if (cleanPin.startsWith('742')) {
      return { pin: cleanPin, district: 'Murshidabad', state: 'West Bengal', area: 'Murshidabad Division' };
    }
    if (cleanPin.startsWith('732')) {
      return { pin: cleanPin, district: 'Malda', state: 'West Bengal', area: 'Malda Division' };
    }
    if (cleanPin.startsWith('735') || cleanPin.startsWith('736')) {
      return { pin: cleanPin, district: 'Jalpaiguri / Cooch Behar', state: 'West Bengal', area: 'North Bengal Division' };
    }
    if (cleanPin.startsWith('110')) {
      return { pin: cleanPin, district: 'Delhi', state: 'Delhi', area: 'National Capital Territory' };
    }
    if (cleanPin.startsWith('400')) {
      return { pin: cleanPin, district: 'Mumbai', state: 'Maharashtra', area: 'Mumbai Metropolitan Region' };
    }
    if (cleanPin.startsWith('411')) {
      return { pin: cleanPin, district: 'Pune', state: 'Maharashtra', area: 'Pune Postal Division' };
    }
    if (cleanPin.startsWith('560')) {
      return { pin: cleanPin, district: 'Bengaluru Urban', state: 'Karnataka', area: 'Bengaluru Postal Division' };
    }
    if (cleanPin.startsWith('600')) {
      return { pin: cleanPin, district: 'Chennai', state: 'Tamil Nadu', area: 'Chennai Postal Division' };
    }
    if (cleanPin.startsWith('500')) {
      return { pin: cleanPin, district: 'Hyderabad', state: 'Telangana', area: 'Hyderabad Postal Division' };
    }
    if (cleanPin.startsWith('201')) {
      return { pin: cleanPin, district: 'Gautam Buddha Nagar / Ghaziabad', state: 'Uttar Pradesh', area: 'Western UP Division' };
    }
    if (cleanPin.startsWith('122')) {
      return { pin: cleanPin, district: 'Gurugram', state: 'Haryana', area: 'Gurugram Postal Division' };
    }

    return null;
  }

  /**
   * Single Source of Truth for Delivery Serviceability.
   *
   * Business Rules:
   * IF district === "Kolkata" -> DELIVERABLE
   * OR IF district === "North 24 Parganas" -> DELIVERABLE
   * OR IF district === "South 24 Parganas" -> DELIVERABLE
   * OTHERWISE -> UNDELIVERABLE
   */
  public checkPinServiceability(pin: string): ServiceabilityResult {
    const cleanPin = (pin || '').toString().trim();

    // 1. Validate PIN format (Must be exactly 6 digits numeric)
    if (!/^[1-9][0-9]{5}$/.test(cleanPin)) {
      return {
        deliverable: false,
        pin: cleanPin,
        district: null,
        state: null,
        allowedDistricts: this.allowedDistricts,
        message: 'Invalid PIN code format. Please enter a valid 6-digit Indian PIN (e.g. 700019, 700091, 700027).',
      };
    }

    // 2. Resolve District
    const resolved = this.resolvePin(cleanPin);

    // 3. Fail closed if unresolved
    if (!resolved) {
      this.logger.warn(`Unresolved PIN lookup attempt: ${cleanPin} -> Rejected`);
      return {
        deliverable: false,
        pin: cleanPin,
        district: null,
        state: null,
        allowedDistricts: this.allowedDistricts,
        message: `Delivery not available for PIN ${cleanPin}. Sorry, ShishaRent currently delivers only within Kolkata, North 24 Parganas and South 24 Parganas.`,
      };
    }

    // 4. Strict 3-District Whitelist Evaluation
    const isAllowed = this.allowedDistricts.includes(resolved.district as AllowedDeliveryDistrict);

    if (!isAllowed) {
      this.logger.warn(
        `Serviceability rejected for PIN ${cleanPin} (District: ${resolved.district}, State: ${resolved.state})`,
      );
      return {
        deliverable: false,
        pin: cleanPin,
        district: resolved.district,
        state: resolved.state,
        area: resolved.area,
        allowedDistricts: this.allowedDistricts,
        message: `Delivery not available in ${resolved.district}, ${resolved.state}. Sorry, ShishaRent currently delivers only within Kolkata, North 24 Parganas and South 24 Parganas.`,
      };
    }

    // 5. Success - Whitelisted District
    const zoneName = this.getZoneNameForDistrict(resolved.district, cleanPin);
    const baseFee = this.getBaseFeeForDistrict(resolved.district);

    return {
      deliverable: true,
      pin: cleanPin,
      district: resolved.district,
      state: resolved.state,
      area: resolved.area,
      zoneName,
      baseDeliveryFee: baseFee,
      allowedDistricts: this.allowedDistricts,
      message: `Delivery available in ${resolved.district} (${resolved.area})`,
    };
  }

  private getZoneNameForDistrict(district: string, pin: string): string {
    if (district === 'North 24 Parganas') {
      if (['700091', '700064', '700097', '700098', '700106'].includes(pin)) {
        return 'Salt Lake Hub (North 24 Parganas)';
      }
      if (['700156', '700157', '700158', '700159', '700160', '700135', '700136'].includes(pin)) {
        return 'New Town & Rajarhat Hub (North 24 Parganas)';
      }
      return 'North 24 Parganas Delivery Network';
    }
    if (district === 'South 24 Parganas') {
      if (['700027', '700034', '700038', '700061', '700063', '700088'].includes(pin)) {
        return 'South West Hub - Behala / Alipore (South 24 Parganas)';
      }
      if (['700084', '700096', '700103', '700150'].includes(pin)) {
        return 'Garia & Sonarpur Hub (South 24 Parganas)';
      }
      return 'South 24 Parganas Delivery Network';
    }
    return 'Kolkata Central & South Fleet';
  }

  private getBaseFeeForDistrict(district: string): number {
    if (district === 'North 24 Parganas' || district === 'South 24 Parganas') {
      return 150.0;
    }
    return 150.0;
  }
}
