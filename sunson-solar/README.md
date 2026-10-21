# Sun Son Solar — Backend (CodeIgniter 4 REST API)

Backend lang (JSON API). Base sa 3 interviews ng Sun Son Solar. Auth ay **Bearer token** (`Authorization: Bearer <token>`).

## Setup

```bash
composer install                 # kukunin ang codeigniter4/framework
cp env .env                      # (kung wala pang .env) tapos i-set ang database.default.*
# gumawa ng empty database na "sunson_solar" sa MySQL (phpMyAdmin / XAMPP)
php spark migrate
php spark db:seed InitialDataSeeder
php spark serve                  # http://localhost:8080
```

## Starting accounts (galing sa interview)

| Who | Username | Password | Role |
|---|---|---|---|
| Katherine Sinagaraw (founder) | `KittyKat16` | `K@tSunShine16` | admin |
| Sol Solis (IT head) | `admin` | `admin123` | admin |

> Palitan agad ang passwords pagkatapos ng handover (`PUT /api/profile/password`). Hashed (bcrypt) ang naka-save sa DB.

## Roles

`customer` · `admin` · `hr` · `dispatcher` · `technician` · `staff` (Sales/Marketing/Accounting/Customer Service, etc.)

- Customers lang ang puwedeng mag-register mag-isa. Employees ay gagawin ng **HR o admin** (`POST /api/admin/users`). Bawal gumawa ng admin ang HR.
- Every user may `id` (primary key), at employees may `department_id`.

## Response format

```json
{ "status": 200, "message": "OK", "data": { } }
```
Errors: `401` no/invalid token · `403` wrong role · `404` · `409` · `422` `{ "errors": { "field": "msg" } }`

## Endpoints

### Public
| Method | URL | Purpose |
|---|---|---|
| POST | `/api/auth/register` | Customer sign-up (first/last/middle name, birthdate, gender, email, phone, address, username, password) |
| POST | `/api/auth/login` | `{username (or email), password}` → token |
| GET | `/api/departments` | Department list |
| GET | `/api/public/home` | Homepage data: tagline, benefits, services, testimonials, FAQs, latest posts |
| GET | `/api/public/services` · `/products?category=` · `/posts` · `/posts/{id}` | Catalog / blog |
| POST | `/api/public/quotes` | "Request a free quote" |

### Any logged-in user
`POST /api/auth/logout` · `GET /api/auth/me` · `GET|PUT /api/profile` · `PUT /api/profile/password` · `PUT /api/profile/preferences` ·
`GET /api/notifications` · `PUT /api/notifications/{id}/read` · `PUT /api/notifications/read-all`

### Customer (`/api/customer/...`)
`GET|POST bookings` · `GET bookings/{id}` · `PUT bookings/{id}/reschedule` · `PUT bookings/{id}/cancel` ·
`GET history` (completed services, payments, total) · `GET receipts/{paymentId}` ·
`GET|POST tickets` · `GET tickets/{id}` · `POST tickets/{id}/reply`

### Technician
- `POST /api/attendance/check-in` — **isang button**: `{latitude, longitude, booking_id?}`. Name at oras ay server ang nagre-record.
- `GET /api/attendance/mine`
- `GET /api/technician/jobs` · `GET /api/technician/jobs/{id}` — customer name, address, service type lang (walang email/phone/birthdate)
- `PUT /api/technician/jobs/{id}/complete`
- `GET|POST /api/technician/incidents` — problem reports, diretso sa admin

### All employees
`GET /api/announcements` (para sa lahat o sa role nila)

### Admin area (`/api/admin/...`)
| Who | Endpoints |
|---|---|
| admin, hr | `users` (list/create/show/update), `users/{id}/deactivate`, `users/{id}/activate`, `announcements` (CRUD) |
| admin, dispatcher, hr | `attendance?user_id=&from=&to=` |
| admin, dispatcher | `bookings?from=&to=&status=&technician_id=` (calendar view + `by_day`), `technicians`, `bookings/{id}/approve`, `/decline`, `/assign`, `/reschedule`, `POST bookings/{id}/payments` |
| admin, staff | `tickets` (list/show/reply/status), `quotes` (list/update) |
| admin | `reports/summary`, `reports/customer-activity`, `reports/service-usage`, `reports/revenue` (chart-ready `labels`/`values`) |
| admin | `notifications/send` (audience: `all_customers`, `all_employees`, `everyone`, `role:technician`, `users`), `notifications` (sent log) |
| admin | `incidents` (list/update) |
| admin | Settings & content CRUD: `services`, `products`, `availability`, `testimonials`, `faqs`, `posts` |

## Business rules built in
- Booking flow: `pending → approved → assigned → in_progress → completed` (o `declined` / `cancelled`).
- Customer reschedule → balik sa `pending`, natatanggal ang technician.
- Availability (admin Settings): kung may naka-set na slots sa isang weekday, bawal mag-book sa labas ng oras o kapag puno na (`max_bookings`).
- Customers na nag-off ng reminders/promotions/system alerts sa preferences ay hindi na pinapadalhan nito (schedule changes ay laging naihahatid).
- Deactivated account = patay na rin ang lahat ng tokens niya. Walang admin na puwedeng mag-deactivate sa sarili.
- Login rate limit: 10 attempts / minuto kada (IP + username). Token expiry: 24 hours.

## Assumptions / dapat i-confirm sa client
1. **Sol's email** — putol sa interview (`sol.solis,`). Naka-seed: `sol.solis@sunsonsolar.com`. Pangalan: Sol (first) / Sun (middle) / Solis (last).
2. **Gender** nina Kat at Sol — hindi nabanggit, kaya `NULL` sa seed.
3. **Apollo Santos** (head technician/dispatcher) — sa unang interview "magre-register on their own", pero sabi ni Sol sa huli employees ay gagawin ng HR/admin. Sinunod ang huli; hindi naka-seed si Apollo.
4. **Prices** ng services ay `0.00` muna — itatakda ng admin sa Settings.
5. Payments ay **ni-record lang ng admin/dispatcher** (cash, GCash, etc.). Wala pang online payment gateway.
6. Chat box ng support = ticket na may thread ng messages (hindi real-time).

## Mga puwedeng idagdag (sa tingin ko kulang pa)
Forgot/reset password via email · email verification · "must change password on first login" para sa admin123 · audit log (sino nag-edit/nag-delete ng ano) · file/photo upload sa incident reports · technician check-out · invoices/quotations na PDF · product inventory movement.
