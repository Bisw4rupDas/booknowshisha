import { Injectable, ExecutionContext } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class JwtAuthGuard extends AuthGuard('jwt') {
  constructor(private readonly configService?: ConfigService) {
    super();
  }

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const request = context.switchToHttp().getRequest();
    if (request && request.headers) {
      const coreSecretHeader = request.headers['x-core-secret'];
      const configuredSecret =
        this.configService?.get<string>('HOOKAH_RENTAL_CORE_SHARED_SECRET') ||
        process.env.HOOKAH_RENTAL_CORE_SHARED_SECRET ||
        'shared_secret_for_internal_plugin_nest_bridge';

      if (coreSecretHeader && coreSecretHeader === configuredSecret) {
        request.user = {
          id: 'bridge-system-user',
          email: 'bridge@booknowshisha.local',
          role: 'ADMIN',
          isBridge: true,
        };
        return true;
      }
    }

    return super.canActivate(context) as Promise<boolean>;
  }
}
