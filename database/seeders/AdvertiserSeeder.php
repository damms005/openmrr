<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Advertiser;
use Illuminate\Database\Seeder;

final class AdvertiserSeeder extends Seeder
{
    public function run(): void
    {
        Advertiser::factory()
            ->count(20)
            ->sequence(
                [
                    'title' => 'PaymentHub',
                    'description' => 'Global payment processing for creators.',
                    'image_url' => $this->getGravatarUrl('payment@hub.dev'),
                    'link_url' => 'https://paymenthub.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'CloudForge',
                    'description' => 'Deploy worldwide with one click.',
                    'image_url' => $this->getGravatarUrl('cloud@forge.io'),
                    'link_url' => 'https://cloudforge.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'DevHub Pro',
                    'description' => 'Manage open source projects effortlessly.',
                    'image_url' => $this->getGravatarUrl('dev@hub.pro'),
                    'link_url' => 'https://devhubpro.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'MetaFrame',
                    'description' => 'Beautiful documentation in minutes.',
                    'image_url' => $this->getGravatarUrl('meta@frame.app'),
                    'link_url' => 'https://metaframe.app',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'SignalStack',
                    'description' => 'Real-time monitoring and alerting.',
                    'image_url' => $this->getGravatarUrl('signal@stack.io'),
                    'link_url' => 'https://signalstack.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'CodeSync',
                    'description' => 'Collaborative IDE for teams.',
                    'image_url' => $this->getGravatarUrl('code@sync.dev'),
                    'link_url' => 'https://codesync.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'DataFlow',
                    'description' => 'Stream analytics made simple.',
                    'image_url' => $this->getGravatarUrl('data@flow.io'),
                    'link_url' => 'https://dataflow.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'ApiGate',
                    'description' => 'Secure API management platform.',
                    'image_url' => $this->getGravatarUrl('api@gate.dev'),
                    'link_url' => 'https://apigate.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'BuildKit',
                    'description' => 'CI/CD pipelines that just work.',
                    'image_url' => $this->getGravatarUrl('build@kit.io'),
                    'link_url' => 'https://buildkit.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'LogStream',
                    'description' => 'Centralized logging for modern apps.',
                    'image_url' => $this->getGravatarUrl('log@stream.dev'),
                    'link_url' => 'https://logstream.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'TestLab',
                    'description' => 'Automated testing at scale.',
                    'image_url' => $this->getGravatarUrl('test@lab.io'),
                    'link_url' => 'https://testlab.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'SecureVault',
                    'description' => 'Enterprise secrets management.',
                    'image_url' => $this->getGravatarUrl('secure@vault.dev'),
                    'link_url' => 'https://securevault.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'MicroGrid',
                    'description' => 'Serverless functions simplified.',
                    'image_url' => $this->getGravatarUrl('micro@grid.io'),
                    'link_url' => 'https://microgrid.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'CacheLayer',
                    'description' => 'Ultra-fast distributed caching.',
                    'image_url' => $this->getGravatarUrl('cache@layer.dev'),
                    'link_url' => 'https://cachelayer.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'FormCraft',
                    'description' => 'Beautiful forms in minutes.',
                    'image_url' => $this->getGravatarUrl('form@craft.io'),
                    'link_url' => 'https://formcraft.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'EmailFlow',
                    'description' => 'Transactional emails that convert.',
                    'image_url' => $this->getGravatarUrl('email@flow.dev'),
                    'link_url' => 'https://emailflow.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'ChartMaker',
                    'description' => 'Interactive data visualizations.',
                    'image_url' => $this->getGravatarUrl('chart@maker.io'),
                    'link_url' => 'https://chartmaker.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'TaskRunner',
                    'description' => 'Background job processing made easy.',
                    'image_url' => $this->getGravatarUrl('task@runner.dev'),
                    'link_url' => 'https://taskrunner.dev',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'SearchBox',
                    'description' => 'Instant search for any application.',
                    'image_url' => $this->getGravatarUrl('search@box.io'),
                    'link_url' => 'https://searchbox.io',
                    'position' => 'sidebar',
                ],
                [
                    'title' => 'FileVault',
                    'description' => 'Secure file storage and sharing.',
                    'image_url' => $this->getGravatarUrl('file@vault.dev'),
                    'link_url' => 'https://filevault.dev',
                    'position' => 'sidebar',
                ]
            )
            ->create();
    }

    private function getGravatarUrl(string $email): string
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s=300&d=identicon";
    }
}
