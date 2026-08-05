![LibreSpeed Logo](https://github.com/librespeed/speedtest/blob/master/.logo/logo3.png?raw=true)

# LibreSpeed MVC

A clean PHP MVC rewrite of the [LibreSpeed](https://github.com/librespeed/speedtest) internet speed test.

No Flash, No Java, No Websocket, No Framework Dependency, Pure PHP MVC.

---

## Overview

**LibreSpeed MVC** takes the battle-tested [LibreSpeed](https://github.com/librespeed/speedtest) frontend and pairs it with a ground-up PHP backend built on a lightweight, custom MVC architecture. The original flat-file PHP scripts have been reorganized into proper Controllers, Models, Services, Middleware, and Views -- making the codebase easier to maintain, extend, and deploy.

The project ships with production-ready Docker images using either **OpenResty** (Nginx + Lua) or **FrankenPHP** (Caddy + embedded PHP), both fine-tuned for high-throughput upload and download handling.

## Features

- **Download** speed measurement
- **Upload** speed measurement
- **Ping** and **Jitter** latency metrics
- **IP Address** and **ISP** detection (online API + offline MaxMind GeoIP)
- **Telemetry** storage with result sharing and unique result links
- **Multiple Points of Test** (configurable server list)
- **Classic & Modern UI** -- switchable via environment variable
- **Stats Dashboard** with authentication
- **Multi-database support** -- SQLite, MySQL, PostgreSQL, Microsoft SQL Server
- **Docker-first** deployment with OpenResty (Lua-accelerated) or FrankenPHP
- **Environment-driven configuration** via `.env` (no code changes needed to configure)

## Compatibility

All modern browsers are supported: Edge, Chrome, Firefox, Safari, and their mobile counterparts.

## Architecture

```
libre-speedtest-mvc/
|
|-- public/                         # Document root (web-accessible)
|   |-- index.php                   # Single entry point (front controller)
|   |-- assets/                     # CSS, JS, images, fonts
|   |-- settings.json               # Frontend test parameters
|   |-- server-list.json            # Multi-server configuration
|   |-- speedtest.js                # Client-side speedtest engine
|   '-- speedtest_worker.js         # Web Worker for measurements
|
|-- app/
|   |-- Config/
|   |   |-- config.php              # Centralized configuration loader
|   |   '-- country_asn.mmdb        # Offline GeoIP database
|   |-- Controllers/
|   |   |-- SpeedtestController.php # Homepage / UI rendering
|   |   |-- EngineController.php    # Download, upload, and IP endpoints
|   |   |-- TelemetryController.php # Result storage and display
|   |   '-- AuthController.php      # Login / logout for stats dashboard
|   |-- Core/
|   |   |-- Core.php                # Bootstrap (autoloader, env, config)
|   |   |-- Router.php              # URI routing with parameter support
|   |   |-- Database.php            # PDO factory (multi-driver)
|   |   |-- Controller.php          # Base controller
|   |   '-- Middleware.php          # Middleware interface
|   |-- Middleware/
|   |   '-- AuthMiddleware.php      # Session-based authentication guard
|   |-- Models/
|   |   '-- Telemetry.php           # Telemetry data model
|   |-- Services/
|   |   |-- SpeedtestService.php    # IP / ISP lookup logic
|   |   '-- TelemetryService.php    # Telemetry business logic
|   |-- Traits/
|   |   |-- RequestHelper.php       # HTTP request utilities
|   |   '-- ViewRenderer.php        # View rendering helper
|   '-- Views/
|       |-- layout/main.php         # Shared HTML layout shell
|       |-- home_classic.php        # Classic UI template
|       |-- home_modern.php         # Modern UI template
|       |-- result.php              # Shareable result page
|       |-- stats.php               # Admin statistics dashboard
|       |-- stats_login.php         # Login form
|       '-- stats_error.php         # Error page
|
|-- bootstrap/
|   '-- app.php                     # Application bootstrap & router init
|
|-- routes/
|   '-- web.php                     # All route definitions
|
|-- docker/
|   |-- Dockerfile                  # OpenResty + PHP-FPM image
|   |-- Dockerfile.frankenphp       # FrankenPHP (Caddy) image
|   |-- nginx.conf                  # OpenResty config with Lua handlers
|   |-- Caddyfile                   # FrankenPHP / Caddy config
|   |-- php.ini                     # PHP overrides (upload limits, buffering)
|   '-- entrypoint.sh              # OpenResty entrypoint script
|
|-- docker-compose.yml              # Compose for OpenResty variant
|-- docker-compose.frankenphp.yml   # Compose for FrankenPHP variant
|-- composer.json                   # PHP dependencies
'-- .env.example                    # Environment variable template
```

### Request Lifecycle

```
Browser --> public/index.php --> bootstrap/app.php --> Router
                                                        |
                                              routes/web.php (match)
                                                        |
                                               Middleware pipeline
                                                        |
                                                   Controller
                                                    /      \
                                              Service      View
                                                |
                                              Model / Database
```

## Server Requirements

- **PHP** 7.4 or newer
- **Composer** for dependency management
- A web server that supports URL rewriting (Apache, Nginx, OpenResty, Caddy, etc.)
- **PDO** extension with the driver matching your database choice
- **GD** extension (for result image generation)
- A reasonably fast internet connection

## Quick Start

### Option 1 -- Docker (Recommended)

The fastest way to get up and running. Choose one of the two images:

**OpenResty** (Nginx + Lua -- download and upload handled at the Nginx layer for maximum throughput):

```bash
docker compose up -d
```

**FrankenPHP** (Caddy with embedded PHP -- simpler architecture, automatic HTTPS):

```bash
docker compose -f docker-compose.frankenphp.yml up -d
```

Both images expose port **8080** by default. Open `http://localhost:8080` in your browser.

### Option 2 -- Manual Installation

1. **Clone the repository** (including the upstream submodule):
   ```bash
   git clone --recurse-submodules https://github.com/dyzulk/libre-speedtest-mvc.git
   cd libre-speedtest-mvc
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Create the environment file**:
   ```bash
   cp .env.example .env
   ```
   Edit `.env` to configure your database, title, telemetry, and other settings (see [Configuration](#configuration) below).

4. **Point your web server** document root to the `public/` directory. Ensure URL rewriting sends all requests to `public/index.php`.

   <details>
   <summary>Apache (.htaccess -- included in public/)</summary>

   The project ships with an `.htaccess` file inside `public/`. Make sure `mod_rewrite` is enabled:
   ```
   a2enmod rewrite
   ```
   </details>

   <details>
   <summary>Nginx</summary>

   ```nginx
   server {
       listen 80;
       root /path/to/libre-speedtest-mvc/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           fastcgi_pass unix:/run/php-fpm/php-fpm.sock;
       }
   }
   ```
   </details>

5. **Visit** `http://yourserver/` and run a speed test.

## Configuration

All configuration is driven by the `.env` file. Copy `.env.example` and adjust the values to match your environment.

### Application

| Variable | Default | Description |
|---|---|---|
| `APP_ENV` | `local` | Environment mode (`local`, `production`) |
| `APP_DEBUG` | `true` | Enable debug output |
| `TZ` | `Asia/Jakarta` | Server timezone |
| `WEBPORT` | `8080` | Port exposed by Docker |
| `TITLE` | `My Speedtest Portal` | Page title shown in the browser |
| `TAGLINE` | `HTML5 Network Speed Test` | Subtitle displayed on the UI |

### Frontend Design

| Variable | Default | Description |
|---|---|---|
| `MODE` | `dual` | Test mode: `dual`, `download`, or `upload` |
| `USE_NEW_DESIGN` | `true` | `true` for the modern UI, `false` for classic |

### Database

| Variable | Default | Description |
|---|---|---|
| `DB_TYPE` | `sqlite` | Database driver: `sqlite`, `mysql`, `postgresql`, `mssql` |
| `DB_SQLITE_FILE` | `database.sqlite` | SQLite file path (relative to project root) |
| `DB_HOST` | `127.0.0.1` | Database host (MySQL / PostgreSQL / MSSQL) |
| `DB_PORT` | *(driver default)* | Database port |
| `DB_NAME` | `speedtest` | Database name |
| `DB_USER` | `root` | Database username |
| `DB_PASS` | *(empty)* | Database password |
| `DB_MSSQL_WINDOWS_AUTH` | `false` | Use Windows authentication for MSSQL |
| `DB_MSSQL_TRUST_CERT` | `true` | Trust server certificate for MSSQL |

### Telemetry

| Variable | Default | Description |
|---|---|---|
| `TELEMETRY` | `true` | Master switch -- disabling this turns off all result storage |
| `SPEEDTEST_PASSWORD` | *(empty)* | Password for the admin stats dashboard |
| `SPEEDTEST_EMAIL` | *(empty)* | Admin contact email |
| `TELEMETRY_OBFUSCATION` | `false` | Obfuscate result IDs in URLs |
| `OBFUSCATION_SALT` | *(empty)* | Salt used when obfuscation is enabled |
| `TELEMETRY_REDACT_IP` | `false` | Redact all IP addresses from stored data |

### ISP / GeoIP Lookup

| Variable | Default | Description |
|---|---|---|
| `IPINFO_APIKEY` | *(empty)* | IPInfo.io API key for online ISP lookup |
| `IPINFO_OFFLINE_DB` | `app/Config/country_asn.mmdb` | Path to offline MaxMind-format GeoIP database |

## Docker Details

Two Docker images are provided, each optimized for different use cases:

### OpenResty (Default)

- **Image base**: `openresty/openresty:alpine`
- **Key advantage**: The `/garbage` (download) and `/empty` (upload) endpoints are handled entirely by **Lua** inside Nginx, bypassing PHP completely. This provides maximum throughput with minimal CPU overhead.
- **Architecture**: OpenResty (Nginx + Lua) reverse-proxies PHP-FPM for MVC routes only.

### FrankenPHP

- **Image base**: `dunglas/frankenphp:latest-php8.3-alpine`
- **Key advantage**: Single-process architecture -- Caddy serves static files and executes PHP in the same process. Simpler to operate, with built-in automatic HTTPS support.
- **Architecture**: FrankenPHP (Caddy + embedded PHP) handles everything.

### Building Manually

```bash
# OpenResty
docker build -f docker/Dockerfile -t libre-speedtest-mvc:openresty .

# FrankenPHP
docker build -f docker/Dockerfile.frankenphp -t libre-speedtest-mvc:frankenphp .
```

## Routes

| Method | Path | Controller | Description |
|---|---|---|---|
| `GET` | `/` | `SpeedtestController@index` | Main speed test page |
| `GET/POST` | `/empty` | `EngineController@empty` | Upload endpoint (accepts and discards POST body) |
| `GET` | `/garbage` | `EngineController@garbage` | Download endpoint (streams random data) |
| `GET` | `/getIP` | `EngineController@getIP` | Returns client IP and ISP info |
| `POST` | `/telemetry` | `TelemetryController@store` | Stores test results |
| `GET` | `/results/{id}` | `TelemetryController@show` | Shareable result page |
| `GET` | `/stats` | `TelemetryController@stats` | Admin dashboard (requires login) |
| `GET/POST` | `/login` | `AuthController` | Authentication |
| `GET` | `/logout` | `AuthController@logout` | Log out |

## Multiple Servers

To configure multiple test servers, edit `public/server-list.json`:

```json
[
  {
    "name": "Local Server",
    "server": "/",
    "id": 1,
    "dlURL": "garbage",
    "ulURL": "empty",
    "pingURL": "empty",
    "getIpURL": "getIP"
  },
  {
    "name": "Remote Server",
    "server": "https://speedtest.example.com/",
    "id": 2,
    "dlURL": "garbage",
    "ulURL": "empty",
    "pingURL": "empty",
    "getIpURL": "getIP"
  }
]
```

## Upstream

This project is built on top of [LibreSpeed](https://github.com/librespeed/speedtest) by [Federico Dossena](https://github.com/adolfintel). The upstream repository is included as a Git submodule under `from/` for reference and attribution.

## License

Copyright (C) 2016-2024 Federico Dossena (original LibreSpeed)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program. If not, see <https://www.gnu.org/licenses/lgpl>.
