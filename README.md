# Stone Inventory Management App

A Laravel + SQLite application tailored for the stone industry (marble, granite, etc.).

## Features

- **Hierarchical Catalog**: Stone Type -> Name -> Color/Finish.
- **Inventory Management**: Track slabs by dimensions (inches), thickness (mm), and compute SQFT automatically.
- **Barcode/QR**: Generate Code128 barcodes and HMAC-signed QR codes.
- **Operations**: Cut/Resize, Reserve, Sell, with audit logs.
- **Roles**: Admin, Manager, Showroom, Clerk.
- **Mobile Quick-Find**: Scan QR codes to instantly view item details.
- **Security**: Role-based access control (Sanctum).

## Setup

### Prerequisites
- PHP 8.2+
- Composer
- SQLite

### Local Development

1. **Clone the repository**
   ```bash
   git clone <url>
   cd stone-inventory
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Ensure `DB_CONNECTION=sqlite` is set in `.env`.

4. **Database Setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```
   
   **Default Credentials:**
   - Email: `admin@example.com`
   - Password: `password`

5. **Run Server**
   ```bash
   php artisan serve
   ```

### Docker / Podman

1. **Build and Run**
   ```bash
   docker-compose up -d --build
   ```
   
   Or with Podman:
   ```bash
   podman-compose up -d --build
   ```

2. **Access App**
   Open http://localhost:8000

## API Documentation

See `routes/api.php` for all endpoints.
- `POST /login`: Get token.
- `GET /api/items`: List items.
- `POST /api/items`: Create item.
- `POST /api/items/{id}/cut`: Cut item.

## Testing

Run unit and feature tests:
```bash
php artisan test
```
