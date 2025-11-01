<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Founder;
use App\Models\Startup;
use Illuminate\Database\Seeder;

final class StartupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $founderData = [
            ['x_handle' => 'alexchen'],
            ['x_handle' => 'sarahkim'],
            ['x_handle' => 'javier_dev'],
            ['x_handle' => 'emily_smith'],
            ['x_handle' => 'marcus_jo'],
            ['x_handle' => 'natasha_p'],
            ['x_handle' => 'lisa_zhang'],
            ['x_handle' => 'carlos_m'],
            ['x_handle' => 'anna_k'],
            ['x_handle' => 'james_t'],
            ['x_handle' => 'priya_s'],
            ['x_handle' => 'tom_anderson'],
            ['x_handle' => 'maya_j'],
            ['x_handle' => 'erik_n'],
            ['x_handle' => 'sofia_r'],
            ['x_handle' => 'kevin_o'],
            ['x_handle' => 'zoe_c'],
            ['x_handle' => 'daniel_k'],
            ['x_handle' => 'rachel_b'],
            ['x_handle' => 'alex_v'],
            ['x_handle' => 'mia_l'],
            ['x_handle' => 'ryan_h'],
            ['x_handle' => 'nina_f'],
            ['x_handle' => 'jason_w'],
        ];

        Founder::insert($founderData);
        $founders = Founder::all();

        $startups = [
            [
                'name' => 'Nexflow',
                'description' => 'Seamless workflow automation for distributed teams',
                'website_url' => 'https://nexflow.dev',
            ],
            [
                'name' => 'Prism Analytics',
                'description' => 'Real-time analytics dashboard with predictive insights',
                'website_url' => 'https://prismanalytics.io',
            ],
            [
                'name' => 'CodeVault',
                'description' => 'Secure code storage and collaboration platform',
                'website_url' => 'https://codevault.sh',
            ],
            [
                'name' => 'TaskMuse',
                'description' => 'AI-powered project management for creative teams',
                'website_url' => 'https://taskmuse.app',
            ],
            [
                'name' => 'Velocity CLI',
                'description' => 'Lightning-fast command-line toolkit for developers',
                'website_url' => 'https://velocitycli.dev',
            ],
            [
                'name' => 'SecureVault Pro',
                'description' => 'Enterprise-grade secrets management and encryption',
                'website_url' => 'https://securevault.pro',
            ],
            [
                'name' => 'DataFlow Stream',
                'description' => 'Real-time data pipeline builder for big data',
                'website_url' => 'https://dataflowstream.io',
            ],
            [
                'name' => 'Lumina Design',
                'description' => 'AI-assisted design system and component library management',
                'website_url' => 'https://luminadesign.app',
            ],
            [
                'name' => 'MeshNetwork',
                'description' => 'Distributed networking infrastructure for edge computing',
                'website_url' => 'https://meshnetwork.dev',
            ],
            [
                'name' => 'CloudSync Pro',
                'description' => 'Multi-cloud file synchronization and backup solution',
                'website_url' => 'https://cloudsync.pro',
            ],
            [
                'name' => 'DevMetrics',
                'description' => 'Engineering productivity analytics for development teams',
                'website_url' => 'https://devmetrics.io',
            ],
            [
                'name' => 'ApiGateway Plus',
                'description' => 'Enterprise API management and monitoring platform',
                'website_url' => 'https://apigateway.plus',
            ],
            [
                'name' => 'FormBuilder Studio',
                'description' => 'Drag-and-drop form builder with advanced integrations',
                'website_url' => 'https://formbuilder.studio',
            ],
            [
                'name' => 'LogStream',
                'description' => 'Real-time log aggregation and analysis platform',
                'website_url' => 'https://logstream.dev',
            ],
            [
                'name' => 'TestSuite AI',
                'description' => 'AI-powered automated testing and quality assurance',
                'website_url' => 'https://testsuite.ai',
            ],
            [
                'name' => 'DocuFlow',
                'description' => 'Document workflow automation for legal and compliance teams',
                'website_url' => 'https://docuflow.app',
            ],
            [
                'name' => 'ChatBot Builder',
                'description' => 'No-code chatbot creation platform with NLP capabilities',
                'website_url' => 'https://chatbotbuilder.io',
            ],
            [
                'name' => 'ServerMonitor Pro',
                'description' => 'Advanced server monitoring and alerting system',
                'website_url' => 'https://servermonitor.pro',
            ],
            [
                'name' => 'EmailCampaign Studio',
                'description' => 'Advanced email marketing automation with AI personalization',
                'website_url' => 'https://emailcampaign.studio',
            ],
            [
                'name' => 'DatabaseOptimizer',
                'description' => 'Automated database performance tuning and optimization',
                'website_url' => 'https://dboptimizer.dev',
            ],
            [
                'name' => 'CRM Connect',
                'description' => 'Customer relationship management with advanced analytics',
                'website_url' => 'https://crmconnect.app',
            ],
            [
                'name' => 'CodeReview Bot',
                'description' => 'AI-powered code review and quality analysis tool',
                'website_url' => 'https://codereview.bot',
            ],
            [
                'name' => 'ProjectSync',
                'description' => 'Cross-platform project synchronization and collaboration',
                'website_url' => 'https://projectsync.io',
            ],
            [
                'name' => 'SecurityScan Pro',
                'description' => 'Comprehensive security vulnerability scanning platform',
                'website_url' => 'https://securityscan.pro',
            ],
            [
                'name' => 'InvoiceFlow',
                'description' => 'Automated invoicing and payment processing for freelancers',
                'website_url' => 'https://invoiceflow.app',
            ],
            [
                'name' => 'BackupVault',
                'description' => 'Encrypted cloud backup solution for businesses',
                'website_url' => 'https://backupvault.io',
            ],
            [
                'name' => 'AnalyticsHub',
                'description' => 'Unified analytics dashboard for multiple data sources',
                'website_url' => 'https://analyticshub.dev',
            ],
            [
                'name' => 'DeployBot',
                'description' => 'Automated deployment pipeline with rollback capabilities',
                'website_url' => 'https://deploybot.dev',
            ],
            [
                'name' => 'ContentCMS',
                'description' => 'Headless content management system with API-first approach',
                'website_url' => 'https://contentcms.io',
            ],
            [
                'name' => 'LoadTester Pro',
                'description' => 'Performance testing and load simulation platform',
                'website_url' => 'https://loadtester.pro',
            ],
            [
                'name' => 'NotificationCenter',
                'description' => 'Multi-channel notification delivery and management system',
                'website_url' => 'https://notificationcenter.app',
            ],
            [
                'name' => 'APIDocGen',
                'description' => 'Automated API documentation generator with interactive examples',
                'website_url' => 'https://apidocgen.dev',
            ],
            [
                'name' => 'TaskQueue Manager',
                'description' => 'Distributed task queue system with priority scheduling',
                'website_url' => 'https://taskqueue.io',
            ],
            [
                'name' => 'FeatureFlag Studio',
                'description' => 'Feature flag management with A/B testing capabilities',
                'website_url' => 'https://featureflag.studio',
            ],
            [
                'name' => 'DataPipeline Builder',
                'description' => 'Visual ETL pipeline builder for data transformation',
                'website_url' => 'https://datapipeline.builder',
            ],
            [
                'name' => 'WebhookRelay',
                'description' => 'Webhook forwarding and transformation service',
                'website_url' => 'https://webhookrelay.io',
            ],
            [
                'name' => 'SchemaValidator',
                'description' => 'JSON and XML schema validation as a service',
                'website_url' => 'https://schemavalidator.dev',
            ],
            [
                'name' => 'CloudFunction Runner',
                'description' => 'Serverless function execution platform with auto-scaling',
                'website_url' => 'https://cloudfunction.run',
            ],
            [
                'name' => 'ErrorTracker Pro',
                'description' => 'Real-time error tracking and debugging platform',
                'website_url' => 'https://errortracker.pro',
            ],
            [
                'name' => 'CacheManager',
                'description' => 'Distributed caching solution with intelligent invalidation',
                'website_url' => 'https://cachemanager.io',
            ],
            [
                'name' => 'SearchEngine Builder',
                'description' => 'Custom search engine creation with AI-powered relevance',
                'website_url' => 'https://searchengine.builder',
            ],
            [
                'name' => 'QueueProcessor',
                'description' => 'High-performance message queue processing system',
                'website_url' => 'https://queueprocessor.dev',
            ],
            [
                'name' => 'FileConverter Pro',
                'description' => 'Batch file conversion service with API integration',
                'website_url' => 'https://fileconverter.pro',
            ],
            [
                'name' => 'DatabaseMigrator',
                'description' => 'Automated database migration and schema management',
                'website_url' => 'https://dbmigrator.io',
            ],
            [
                'name' => 'ImageOptimizer',
                'description' => 'Automated image compression and optimization service',
                'website_url' => 'https://imageoptimizer.app',
            ],
            [
                'name' => 'ConfigManager Pro',
                'description' => 'Centralized configuration management for microservices',
                'website_url' => 'https://configmanager.pro',
            ],
            [
                'name' => 'MetricsCollector',
                'description' => 'Custom metrics collection and visualization platform',
                'website_url' => 'https://metricscollector.dev',
            ],
            [
                'name' => 'AuthProvider',
                'description' => 'OAuth and SSO authentication service for developers',
                'website_url' => 'https://authprovider.io',
            ],
            [
                'name' => 'LogAnalyzer Pro',
                'description' => 'Advanced log analysis with machine learning insights',
                'website_url' => 'https://loganalyzer.pro',
            ],
            [
                'name' => 'CDNAccelerator',
                'description' => 'Global content delivery network with edge optimization',
                'website_url' => 'https://cdnaccelerator.io',
            ],
            [
                'name' => 'DataValidator',
                'description' => 'Real-time data validation and cleansing service',
                'website_url' => 'https://datavalidator.app',
            ],
            [
                'name' => 'WorkflowEngine',
                'description' => 'Business process automation with visual workflow designer',
                'website_url' => 'https://workflowengine.dev',
            ],
            [
                'name' => 'APIThrottler',
                'description' => 'Rate limiting and API throttling service',
                'website_url' => 'https://apithrottler.io',
            ],
            [
                'name' => 'EventStreamer',
                'description' => 'Real-time event streaming and processing platform',
                'website_url' => 'https://eventstreamer.dev',
            ],
            [
                'name' => 'TemplateEngine Pro',
                'description' => 'Dynamic template generation with multi-format output',
                'website_url' => 'https://templateengine.pro',
            ],
            [
                'name' => 'CompressionService',
                'description' => 'High-performance data compression and decompression API',
                'website_url' => 'https://compressionservice.io',
            ],
            [
                'name' => 'SessionManager',
                'description' => 'Distributed session management for web applications',
                'website_url' => 'https://sessionmanager.app',
            ],
            [
                'name' => 'PaymentGateway Plus',
                'description' => 'Multi-provider payment processing with fraud detection',
                'website_url' => 'https://paymentgateway.plus',
            ],
            [
                'name' => 'GeolocationAPI',
                'description' => 'Accurate geolocation and geocoding service',
                'website_url' => 'https://geolocation.api',
            ],
            [
                'name' => 'TranslationHub',
                'description' => 'AI-powered translation service with context awareness',
                'website_url' => 'https://translationhub.io',
            ],
            [
                'name' => 'SchedulerPro',
                'description' => 'Advanced job scheduling with dependency management',
                'website_url' => 'https://scheduler.pro',
            ],
            [
                'name' => 'VideoProcessor',
                'description' => 'Cloud-based video encoding and streaming service',
                'website_url' => 'https://videoprocessor.dev',
            ],
            [
                'name' => 'TextAnalyzer AI',
                'description' => 'Natural language processing and sentiment analysis API',
                'website_url' => 'https://textanalyzer.ai',
            ],
            [
                'name' => 'NetworkMonitor',
                'description' => 'Network performance monitoring and diagnostics platform',
                'website_url' => 'https://networkmonitor.io',
            ],
            [
                'name' => 'DocumentParser',
                'description' => 'Intelligent document parsing and data extraction service',
                'website_url' => 'https://documentparser.app',
            ],
            [
                'name' => 'CloudStorage Pro',
                'description' => 'Enterprise cloud storage with advanced encryption',
                'website_url' => 'https://cloudstorage.pro',
            ],
            [
                'name' => 'APIGateway Lite',
                'description' => 'Lightweight API gateway for microservices architecture',
                'website_url' => 'https://apigateway.lite',
            ],
        ];

        $startupRecords = [];

        foreach ($startups as $startupData) {
            $mrr = fake()->numberBetween(2000, 40000);
            $defaults = Startup::factory()->make()->toArray();

            $startupRecords[] = [
                ...$defaults,
                'founder_id' => $founders->random()->id,
                'slug' => str($startupData['name'])->slug(),
                'last_synced_at' => now(),
                'total_revenue' => fake()->numberBetween($mrr * 6, $mrr * 24),
                'monthly_recurring_revenue' => $mrr,
                'subscriber_count' => fake()->numberBetween(50, 600),
                ...$startupData,
            ];
        }

        $startupRecords = collect($startupRecords)
            ->sortByDesc('total_revenue')
            ->values()
            ->map(fn($startup, $index) => [...$startup, 'rank' => $index + 1])
            ->toArray();

        Startup::insert($startupRecords);
    }
}
