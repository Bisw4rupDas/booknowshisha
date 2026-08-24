import { Module } from '@nestjs/common';
import { FlavoursController } from './flavours.controller';
import { FlavoursService } from './flavours.service';

@Module({
  controllers: [FlavoursController],
  providers: [FlavoursService],
  exports: [FlavoursService],
})
export class FlavoursModule {}
