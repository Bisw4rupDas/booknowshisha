import { Module } from '@nestjs/common';
import { DamageController } from './damage.controller';
import { DamageService } from './damage.service';
import { PrismaModule } from '../prisma/prisma.module';

@Module({
  imports: [PrismaModule],
  controllers: [DamageController],
  providers: [DamageService],
  exports: [DamageService],
})
export class DamageModule {}
