# Development Environment Setup

This project uses Podman Compose to simulate a shared hosting environment (Apache + PHP) for local development.

## Quick Start

```bash
# Pull required images
podman-compose pull

# Start the development environment
podman-compose up -d

# View logs
podman-compose logs -f

# Stop the environment
podman-compose down
```

## Access Points

| Service | Port | Access URL |
|---------|------|------------|
| Web Server (Apache) | 8080 | http://localhost:8080 |

Links are AES encrypted with a static key - no database required.

## Directory Structure

```
.
├── podman-compose.yml   # Main compose file
├── php.ini.local        # PHP configuration
├── .env                 # Environment variables
├── .gitignore           # Git ignore file
└── DEV_ENVIRONMENT.md   # This file
```

## Customization

### Change Database Passwords

Edit the `.env` file to modify database credentials:
- `MYSQL_ROOT_PASSWORD` - Root password
- `MYSQL_PASSWORD` - User password

### Change PHP Settings

Edit `php.ini.local` for custom PHP configurations:
- Memory limit
- Upload sizes
- Timeout settings

## Troubleshooting

### Container won't start
```bash
# Check logs
podman-compose logs

# Verify images are pulled
podman images
```

### Port 8080 is already in use
```bash
# Change the port in podman-compose.yml
# Change: - "8080:80"
# To: - "8081:80"
```

### Restart containers
```bash
podman-compose restart
```

## Security Notes

- The `.env` file should not be committed to version control (already in .gitignore)
- Default passwords are suitable for development only
- In production, use stronger passwords and disable unnecessary features

## Requirements

- Podman 4.x or later
- podman-compose (or podman with compose support)
- At least 512MB RAM (recommended 1GB+)
- Port 8080 available

## Additional Commands

```bash
# Enter the web container
podman exec -it psical-dev /bin/bash

# Enter the database container
podman exec -it psical-db mariadb

# Run tests
podman-compose exec web php artisan test

# Rebuild images
podman-compose build