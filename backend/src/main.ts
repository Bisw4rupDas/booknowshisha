import { NestFactory } from '@nestjs/core';
import { ValidationPipe, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { SwaggerModule, DocumentBuilder } from '@nestjs/swagger';
import helmet from 'helmet';
import { AppModule } from './app.module';
import { AllExceptionsFilter } from './common/filters/all-exceptions.filter';
import { LoggingInterceptor } from './common/interceptors/logging.interceptor';

async function bootstrap() {
  const logger = new Logger('Bootstrap');
  const app = await NestFactory.create(AppModule);

  const configService = app.get(ConfigService);
  const port = process.env.PORT || configService.get<string | number>('PORT', 3000);
  const apiPrefix = configService.get<string>('API_PREFIX', 'api');

  // Security Headers
  app.use(helmet());

  // Dynamic CORS Configuration
  const corsOriginsRaw = configService.get<string>('CORS_ORIGINS');
  const allowedOrigins = corsOriginsRaw && corsOriginsRaw.trim() !== '*'
    ? corsOriginsRaw.split(',').map((o) => o.trim()).filter(Boolean)
    : true;

  app.enableCors({
    origin: allowedOrigins,
    credentials: true,
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'X-Core-Secret'],
  });

  // Global Prefix
  app.setGlobalPrefix(apiPrefix);

  // Global Validation Pipe
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      transform: true,
      forbidNonWhitelisted: true,
      transformOptions: {
        enableImplicitConversion: true,
      },
    }),
  );

  // Global Filters & Interceptors
  app.useGlobalFilters(new AllExceptionsFilter());
  app.useGlobalInterceptors(new LoggingInterceptor());

  // OpenAPI / Swagger Documentation
  const swaggerConfig = new DocumentBuilder()
    .setTitle('ShishaRent API')
    .setDescription('Core REST API & Business Logic Engine for ShishaRent Rental Platform')
    .setVersion('1.0.0')
    .addBearerAuth()
    .addTag('Health', 'Diagnostic and infrastructure health endpoints')
    .build();

  const document = SwaggerModule.createDocument(app, swaggerConfig);
  SwaggerModule.setup(`${apiPrefix}/docs`, app, document);

  // Enable Graceful Shutdown
  app.enableShutdownHooks();

  await app.listen(port);
  logger.log(`====================================================`);
  logger.log(`🚀 ShishaRent API running on port/socket: ${port} (prefix: /${apiPrefix})`);
  logger.log(`📚 Swagger Documentation: /${apiPrefix}/docs`);
  logger.log(`❤️  Health Check: /${apiPrefix}/health`);
  logger.log(`====================================================`);
}

bootstrap();
