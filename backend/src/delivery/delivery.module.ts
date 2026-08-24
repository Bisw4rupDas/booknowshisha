import { Module } from '@nestjs/common';
import { DeliveryController } from './delivery.controller';
import { DeliveryService } from './delivery.service';
import { PinServiceabilityService } from './serviceability/pin-serviceability.service';

@Module({
  controllers: [DeliveryController],
  providers: [DeliveryService, PinServiceabilityService],
  exports: [DeliveryService, PinServiceabilityService],
})
export class DeliveryModule {}
