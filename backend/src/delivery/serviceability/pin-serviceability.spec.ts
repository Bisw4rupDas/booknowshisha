import { Test, TestingModule } from '@nestjs/testing';
import { PinServiceabilityService } from './pin-serviceability.service';
import { ALLOWED_DELIVERY_DISTRICTS } from './pin-serviceability.data';

describe('PinServiceabilityService (Strict 3-District Whitelist)', () => {
  let service: PinServiceabilityService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [PinServiceabilityService],
    }).compile();

    service = module.get<PinServiceabilityService>(PinServiceabilityService);
  });

  it('should be defined with exactly 3 allowed delivery districts', () => {
    expect(service).toBeDefined();
    expect(service.allowedDistricts).toEqual(['Kolkata', 'North 24 Parganas', 'South 24 Parganas']);
    expect(service.allowedDistricts.length).toBe(3);
  });

  // =========================================================================
  // TEST GROUP A — ALLOWED DISTRICTS (Must Pass)
  // =========================================================================
  describe('TEST GROUP A — ALLOWED (Kolkata, North 24 Parganas, South 24 Parganas)', () => {
    it('should PASS for valid Kolkata district PINs', () => {
      const kolkataPins = ['700019', '700001', '700016', '700020', '700026', '700071'];
      for (const pin of kolkataPins) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(true);
        expect(result.district).toBe('Kolkata');
        expect(result.state).toBe('West Bengal');
        expect(result.message).toContain('Delivery available in Kolkata');
      }
    });

    it('should PASS for valid North 24 Parganas district PINs', () => {
      const north24Pins = ['700091', '700064', '700156', '700160', '700135', '700055', '700048', '700124', '743263'];
      for (const pin of north24Pins) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(true);
        expect(result.district).toBe('North 24 Parganas');
        expect(result.state).toBe('West Bengal');
        expect(result.message).toContain('Delivery available in North 24 Parganas');
      }
    });

    it('should PASS for valid South 24 Parganas district PINs', () => {
      const south24Pins = ['700027', '700034', '700038', '700061', '700084', '700103', '700141', '700150', '743302', '743331'];
      for (const pin of south24Pins) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(true);
        expect(result.district).toBe('South 24 Parganas');
        expect(result.state).toBe('West Bengal');
        expect(result.message).toContain('Delivery available in South 24 Parganas');
      }
    });
  });

  // =========================================================================
  // TEST GROUP B — REJECTED DISTRICTS & STATES (Must Fail)
  // =========================================================================
  describe('TEST GROUP B — REJECTED (Other WB Districts, Other States, Invalid Formats)', () => {
    it('should REJECT another West Bengal district (Howrah)', () => {
      const howrahPins = ['711101', '711102', '711105', '711106', '711109'];
      for (const pin of howrahPins) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(false);
        expect(result.district).toBe('Howrah');
        expect(result.state).toBe('West Bengal');
        expect(result.message).toContain('Delivery not available in Howrah');
        expect(result.message).toContain('Kolkata, North 24 Parganas and South 24 Parganas');
      }
    });

    it('should REJECT another West Bengal district (Hooghly)', () => {
      const hooghlyPins = ['712101', '712122', '712201', '712232', '712258'];
      for (const pin of hooghlyPins) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(false);
        expect(result.district).toBe('Hooghly');
        expect(result.state).toBe('West Bengal');
        expect(result.message).toContain('Delivery not available in Hooghly');
        expect(result.message).toContain('Kolkata, North 24 Parganas and South 24 Parganas');
      }
    });

    it('should REJECT another West Bengal district (Nadia, Darjeeling, Bardhaman)', () => {
      const otherWBPins = [
        { pin: '741101', expectedDist: 'Nadia' },
        { pin: '734001', expectedDist: 'Darjeeling' },
        { pin: '713101', expectedDist: 'Purba Bardhaman' },
        { pin: '713201', expectedDist: 'Paschim Bardhaman' },
      ];
      for (const item of otherWBPins) {
        const result = service.checkPinServiceability(item.pin);
        expect(result.deliverable).toBe(false);
        expect(result.district).toContain(item.expectedDist);
        expect(result.message).toContain('Delivery not available');
        expect(result.message).toContain('Kolkata, North 24 Parganas and South 24 Parganas');
      }
    });

    it('should REJECT another Indian state (Delhi, Mumbai, Bengaluru, Chennai, Hyderabad)', () => {
      const outOfStatePins = [
        { pin: '110001', state: 'Delhi' },
        { pin: '110016', state: 'Delhi' },
        { pin: '400001', state: 'Maharashtra' },
        { pin: '400050', state: 'Maharashtra' },
        { pin: '560001', state: 'Karnataka' },
        { pin: '600001', state: 'Tamil Nadu' },
        { pin: '500001', state: 'Telangana' },
        { pin: '201301', state: 'Uttar Pradesh' },
      ];
      for (const item of outOfStatePins) {
        const result = service.checkPinServiceability(item.pin);
        expect(result.deliverable).toBe(false);
        expect(result.state).toBe(item.state);
        expect(result.message).toContain('Delivery not available');
        expect(result.message).toContain('Kolkata, North 24 Parganas and South 24 Parganas');
      }
    });

    it('should REJECT unknown 6-digit PINs (Fails Closed)', () => {
      const unknownPins = ['999999', '888888', '199999', '399999'];
      for (const pin of unknownPins) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(false);
        expect(result.district).toBeNull();
        expect(result.message).toContain('Kolkata, North 24 Parganas and South 24 Parganas');
      }
    });

    it('should REJECT malformed / invalid length PINs (5 digits, 7 digits, non-numeric)', () => {
      const malformed = ['70001', '7000019', '70000A', 'ABCDEF', '', '   ', '000000', 'null'];
      for (const pin of malformed) {
        const result = service.checkPinServiceability(pin);
        expect(result.deliverable).toBe(false);
        expect(result.district).toBeNull();
      }
    });
  });

  // =========================================================================
  // TEST GROUP C — SECURITY & BYPASS PREVENTION
  // =========================================================================
  describe('TEST GROUP C — SECURITY (No Customer Overrides)', () => {
    it('should strictly evaluate based on PIN resolution and not external claims', () => {
      // If someone claims city="Kolkata" but provides a Howrah PIN (711101)
      const result = service.checkPinServiceability('711101');
      expect(result.deliverable).toBe(false);
      expect(result.district).toBe('Howrah'); // Authoritatively resolved to Howrah

      // If someone claims city="Kolkata" but provides a Delhi PIN (110001)
      const resultDelhi = service.checkPinServiceability('110001');
      expect(resultDelhi.deliverable).toBe(false);
      expect(resultDelhi.district).toBe('New Delhi');
    });
  });
});
