# OpenMRR

OpenMRR is a transparent, searchable database of verified startup revenue. It aggregates revenue data from indie creators and startups, making financial metrics publicly accessible and verifiable.

## Features

- **Polar.sh**: Supports businesses running on [Polar.sh](https://polar.sh)
- **Verified Comments**: Request comments from your customers and use them as verified comments on your website and advert copies to boost brand trust
- **Verified Revenue**: All data sourced directly from the payment API
- **Free Backlinks**: Provides valuable backlinks to listed businesses, improving their SEO and online visibility
- **pSEO With Business Categories**: Revenue categorized by industry, also helps with pSEO
- **Flexible Submission**: Simple submission process with encrypted credential storage
- **Startup Details**: Project revenue breakdowns with trend analysis
- **Founder Aggregation**: View combined revenue across startups per founder
- **Searchable Database**: Find startups and founders with instant search
- **Revenue Analytics**: Widgets displaying MRR trends, total gross revenue, and monthly aggregates
- **Periodic Data Update**: Automated data synchronization with the payment platform
- **Secured**: Data encryption at rest and in transit
- **Full Critical Paths Test Coverage**: All critical business logic have covering automated tests

## Tech Stack

- **PHP**: v8.3+
- **Laravel**: v12
- **Livewire**: v3
- **Filament**: v4
- **Tailwind CSS**: v4
- **Testing**: Pest v4

## Quick Start

### Prerequisites

- PHP 8.4+
- Composer
- Node.js & npm

### Installation

1. Clone the repository and install dependencies:

```bash
git clone https://github.com/damms005/openmrr.git
cd openmrr
composer install
npm install
```

2. Set up environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Initialize database:

```bash
php artisan migrate
php artisan db:seed
```

4. Build assets and run development server:

```bash
composer dev
```

### Development Workflow

For active development with auto-reload:

```bash
composer run dev
```

This starts the application server, Vite development server, and queue worker together.

Or individually:

```bash
php artisan serve
npm run dev
```

## Development Guide

All code should follow Laravel conventions, and adhere to [Spatie's guidelines](https://spatie.be/guidelines) as much as possible.

### Code Standards

Format code with Pint before committing:

```bash
vendor/bin/pint --dirty
```

### Testing

Use [Pest](https://pestphp.com) testing framework to write tests:

Run tests:

```bash
php artisan test
```

## Security

- API keys are encrypted at rest using [Laravel's encryption](https://laravel.com/docs/master/encryption) (OpenSSL AES-256 and AES-128 encryption)
- All payment API communications are read-only (no modifications via API)

### Proof of Code (POCO)

OpenMRR implements Proof of Code verification to cryptographically prove that the running server code matches the open-source code on GitHub.

#### How It Works

1. The server exposes a `/poco` endpoint that uses the latest git commit hash as the encryption key
2. Copy the latest commit hash from the GitHub repository UI
3. Encrypt any value (e.g., "verify") using the commit hash as the encryption secret
4. Send the encrypted token: `GET /poco?token=<encrypted_value>`
5. The server attempts to decrypt the token using its current commit hash

#### Verification Process

**Step 1: Get Latest Commit Hash from GitHub**

Visit [https://github.com/damms005/openmrr](https://github.com/damms005/openmrr) and copy the full commit hash of the latest commit.

**Step 2: Encrypt Your Value**

Run this command, replacing `latest_commit_hash_here` with the full hash from GitHub and `verify` with your value:

```bash
COMMIT_HASH="latest_commit_hash_here" VALUE="verify" KEY=$(echo -n "$COMMIT_HASH" | sha256sum | awk '{print $1}') IV=$(openssl rand -hex 16) ENCRYPTED=$(echo -n "$VALUE" | openssl enc -aes-256-cbc -K "$KEY" -iv "$IV" -a -nosalt) PAYLOAD=$(echo -n "$(echo "$IV" | xxd -r -p)$(echo "$ENCRYPTED" | base64 -d)" | base64) && echo "$PAYLOAD"
```

The output is your encrypted token. Use this token in the next step below:

**Step 3: Verify on Server**

```bash
curl "https://openmrr.com/poco?token=<your-encrypted-token>"

# Success Response:
{
  "verified": true,
  "message": "Code verified successfully",
  "decrypted_value": "verify",
  "commit_hash": "be2682cbbbbe9d4471066a4bf32cc0daa18c4b60"
}

```

## Contributing

Contributions welcome. Follow these steps:

1. Create a feature branch
2. Make changes with corresponding tests where necessary
3. Run `php artisan test` to ensure no regressions
4. Run `vendor/bin/pint --dirty` to format code
5. Submit pull request

## Credits

OpenMRR is built with amazing open-source technologies and inspired by innovative creators:

- **[@levelsio](https://x.com/levelsio)** - Posted about the ['fake MRR' problem](https://x.com/levelsio/status/1983110741033996484)
- **[@marc_louvion](https://x.com/marc_louvion)** - Came up with [TrustMRR](https://trustmrr.com) that inspired OpenMRR following @levelsio's post.
- **[Laravel](https://laravel.com)** - The PHP framework for web artisans
- **[Filament PHP](https://filamentphp.com)** - Beautiful admin panels and UI components for Laravel applications
- **[Pest PHP](https://pestphp.com)** - An elegant PHP testing framework
- **[Tailwind CSS](https://tailwindcss.com)** - The best utility-first CSS framework on planet earth!

## License

GNU General Public License v3.0
