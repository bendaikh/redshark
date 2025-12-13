# License Protection System

This document explains how the license protection system works and how to use it.

## Overview

The licensing system provides:

1. **Domain Binding** - Licenses are tied to specific domains
2. **Offline Validation** - Works without internet (with grace period)
3. **Online Validation** - Periodic checks with your license server
4. **Tamper Protection** - Signed license keys prevent modification
5. **Remote Revocation** - Revoke licenses from your server

---

## How It Works

```
┌─────────────────────┐         ┌─────────────────────┐
│   CLIENT SERVER     │         │   YOUR LICENSE      │
│   (Your App)        │◄───────►│   SERVER            │
│                     │         │                     │
│  - Validates key    │  HTTP   │  - Stores licenses  │
│  - Checks domain    │         │  - Validates online │
│  - Grace period     │         │  - Can revoke       │
└─────────────────────┘         └─────────────────────┘
```

---

## For You (The Developer)

### Setting Up Your License Server

1. **Deploy a separate Laravel instance** as your license server
2. **Run the migrations** on your license server:

```bash
php artisan migrate
```

3. **Add these to your license server's `.env`**:

```env
LICENSE_SECRET=your-super-secret-key-change-this
LICENSE_PRODUCT_ID=redshark
```

> ⚠️ **IMPORTANT**: Keep `LICENSE_SECRET` secret! Only your license server should have it.

### Generating Licenses

**Method 1: Using Artisan Command (Recommended for Database)**

```bash
# Create a license and store in database
php artisan license:create --client="Acme Corp" --email="client@acme.com" --domain="acme.com" --type=PRO --expires=2025-12-31

# List all licenses
php artisan license:list

# List expiring licenses
php artisan license:list --expiring

# Revoke a license
php artisan license:revoke 1  # By ID
php artisan license:revoke "REDS-STD-..."  # By key
```

**Method 2: Generate Key Only (Offline)**

```bash
# Generate a license key (for offline validation)
php artisan license:generate acme.com --type=PRO --expires=2025-12-31

# Generate a development/any-domain key
php artisan license:generate "*" --type=STD
```

### License Types

| Type | Code | Description |
|------|------|-------------|
| Standard | `STD` | Basic features |
| Professional | `PRO` | Advanced features |
| Enterprise | `ENT` | All features + support |

---

## For Clients (Installation)

### Option 1: Environment Variables

Add to `.env`:

```env
LICENSE_KEY=REDS-STD-XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX
LICENSE_DOMAIN=client-domain.com
LICENSE_SERVER_URL=https://your-license-server.com
```

### Option 2: Web Activation

1. Navigate to any page - you'll be redirected to `/license/activate`
2. Enter your license key
3. Click "Activate License"

---

## API Endpoints (License Server)

### Validate License

```http
POST /api/license/validate
Content-Type: application/json

{
  "license_key": "REDS-STD-...",
  "domain": "client.com",
  "product_id": "redshark",
  "server_id": "unique-server-hash"
}
```

### Activate License

```http
POST /api/license/activate
Content-Type: application/json

{
  "license_key": "REDS-STD-...",
  "domain": "client.com",
  "product_id": "redshark",
  "server_id": "unique-server-hash"
}
```

### Revoke License (Admin)

```http
POST /api/license/revoke
Content-Type: application/json

{
  "license_key": "REDS-STD-...",
  "admin_secret": "your-license-secret"
}
```

---

## Configuration Options

In `config/license.php`:

| Option | Default | Description |
|--------|---------|-------------|
| `server_url` | - | Your license server URL |
| `key` | - | The license key |
| `domain` | - | Bound domain |
| `grace_period` | 7 | Days app works without server |
| `validation_interval` | 24 | Hours between online checks |
| `secret` | - | Secret for signing keys |
| `product_id` | `redshark` | Your product identifier |

---

## Security Notes

1. **Never share `LICENSE_SECRET`** - This is used to sign licenses
2. **Use HTTPS** for your license server
3. **The license cache is signed** - Clients can't tamper with it
4. **Domain binding prevents copying** - License only works on authorized domain
5. **Server ID binding** - Detects if license is copied to another server

---

## Troubleshooting

### "License is not valid for this domain"

The license was generated for a different domain. Generate a new license for the correct domain.

### "Maximum activations reached"

The license has been activated on too many servers. Either:
- Increase `max_activations` in the database
- Revoke old activations
- Issue a new license

### "License has expired"

The license expiration date has passed. Issue a new license.

### App works but shows "grace period" warning

Your license server is unreachable. Check:
- License server is running
- `LICENSE_SERVER_URL` is correct
- No firewall blocking the connection

---

## Files Structure

```
app/
├── Console/Commands/
│   ├── GenerateLicense.php    # Generate key (offline)
│   ├── CreateLicense.php      # Create in database
│   ├── CheckLicense.php       # Check current status
│   ├── ListLicenses.php       # List all licenses
│   └── RevokeLicense.php      # Revoke a license
├── Http/
│   ├── Controllers/
│   │   ├── LicenseController.php      # Client activation
│   │   └── Api/
│   │       └── LicenseValidationController.php  # Server API
│   └── Middleware/
│       └── LicenseMiddleware.php      # Route protection
├── Models/
│   ├── License.php            # License model (server)
│   └── LicenseActivation.php  # Activation tracking
└── Services/
    └── LicenseService.php     # Core logic

config/
└── license.php                # Configuration

routes/
├── license.php                # Client routes
└── api.php                    # Server API routes

resources/views/license/
└── activate.blade.php         # Activation page

database/migrations/
├── create_licenses_table.php
└── create_license_activations_table.php
```

---

## Quick Start

### 1. Set up your license server

```bash
# On YOUR server
composer require laravel/framework
php artisan migrate
```

### 2. Generate a license for a client

```bash
php artisan license:create \
  --client="Client Name" \
  --domain="client-domain.com" \
  --type=STD \
  --expires=2025-12-31
```

### 3. Give the client their `.env` settings

```env
LICENSE_KEY=REDS-STD-XXXXXXXX-...
LICENSE_SERVER_URL=https://your-license-server.com
```

### 4. Deploy to client

The client installs the app and it's automatically protected!

