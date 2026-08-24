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
  const port = configService.get<number>('PORT', 3000);
  const apiPrefix = configService.get<string>('API_PREFIX', 'api');

  // Security Headers
  app.use(helmet());

  // Enable CORS
  app.enableCors({
    origin: true,
    credentials: true,
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
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
  logger.log(`🚀 ShishaRent API running on: http://localhost:${port}/${apiPrefix}`);
  logger.log(`📚 Swagger Documentation: http://localhost:${port}/${apiPrefix}/docs`);
  logger.log(`❤️  Health Check: http://localhost:${port}/${apiPrefix}/health`);
  logger.log(`====================================================`);
}

bootstrap();
