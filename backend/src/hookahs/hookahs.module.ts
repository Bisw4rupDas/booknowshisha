import { Module } from '@nestjs/common';
import { HookahsController } from './hookahs.controller';
import { HookahsService } from './hookahs.service';

@Module({
  controllers: [HookahsController],
  providers: [HookahsService],
  exports: [HookahsService],
})
export class HookahsModule {}
