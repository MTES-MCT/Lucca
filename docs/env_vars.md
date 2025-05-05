# ⚙️ Environment Variables — Lucca Project

This Symfony project uses Docker and relies on multiple environment variables for proper configuration and runtime behavior.  
Below is a categorized explanation of each available variable.

---

## 🐳 Docker / Web Server

| Variable       | Description |
|----------------|-------------|
| `SERVER_NAME`  | List of domains handled by the reverse proxy (e.g. `demo-lucca.local`, `admin-lucca.local`, etc.).

---

## ⚙️ Symfony Framework

| Variable              | Description |
|------------------------|-------------|
| `APP_ENV`              | Symfony environment (`dev`, `prod`, etc.) |
| `APP_SECRET`           | Application secret used for internal hashing and token generation |
| `APP_LOCALE`           | Default language of the application (`fr`) |
| `APP_LOCALES_AUTHORIZED` | Authorized locales, separated by `|` (e.g. `fr|gb`) |
| `TRUSTED_IPS`          | List of trusted client IP addresses |
| `TRUSTED_PROXIES`      | List of proxy IPs Symfony should trust (e.g. Docker gateway IPs) |

---

## 🗄️ Database (Doctrine / MariaDB)

| Variable              | Description |
|------------------------|-------------|
| `MYSQL_HOST`           | Hostname of the database container |
| `MYSQL_PORT`           | Port used to connect to the DB |
| `MYSQL_DATABASE`       | Name of the database |
| `MYSQL_USER`           | Username for DB access |
| `MYSQL_PASSWORD`       | Password for the DB user |
| `MYSQL_ROOT_PASSWORD`  | Root password for MariaDB |
| `MYSQL_VERSION`        | MariaDB version used |
| `MYSQL_FULL_VERSION`   | Full MariaDB version string (e.g. `11.8.1-MariaDB`) |

> 🔗 Used to compose the `DATABASE_URL` in Symfony.

---

## 📬 Mailer (Symfony Mailer)

| Variable              | Description |
|------------------------|-------------|
| `MAILER_TRANSPORT`     | Transport type (`sendmail`, `smtp`, `null`, etc.) |
| `MAILER_HOST`          | SMTP host |
| `MAILER_USER`          | SMTP username |
| `MAILER_PASSWORD`      | SMTP password |
| `MAILER_DSN`           | Complete DSN, overrides the individual values if set |

---

## 👤 Default Email Sender (FOSUserBundle)

| Variable                  | Description |
|----------------------------|-------------|
| `FROM_EMAIL_ADDRESS`       | Default sender email |
| `FROM_EMAIL_SENDER_NAME`   | Default sender name |

---

## 🏗️ Lucca Core Bundles

| Variable                        | Description                                            |
|----------------------------------|--------------------------------------------------------|
| `DEFAULT_URL_AFTER_LOGIN`         | Route to redirect to after login                       |
| `DEFAULT_ADMIN_URL_AFTER_LOGIN`   | Admin route to redirect to after login                 |
| `LUCCA_ADMIN_DOMAIN`              | Hostname used for admin routes                         |
| `LUCCA_STRATEGY_ASSET_VERSION`    | Front asset version for cache busting                  |
| `LUCCA_STRATEGY_ASSET_FORCE_REFRESH` | Forces asset cache refresh                             |
| `LUCCA_SNAPPY_FOLDER`             | Temp folder used for PDF generation                    |
| `LUCCA_UPLOAD_DIR`                | Uploads medias destination folder                      |
| `LUCCA_UPLOAD_TEMP_DIR`           | Temporary files folder to store medias                 |
| `LUCCA_PDF_URL`                   | Base path for generating PDFs                          |
| `LUCCA_UPLOAD_MAX_FILE_SIZE`      | Maximum size of a single uploaded file (in MB)         |
| `LUCCA_UPLOAD_MAX_COLLECTION_SIZE`| Max number of files per upload collection              |
| `LUCCA_AVOID_BREAK_PAGE`          | Configuration to prevent page breaks in generated PDFs |
| `LUCCA_GOOGLE_ANALYTICS_ID`       | Google Analytics ID (can be `null`)                    |

---

## 🧪 Unit Testing

| Variable                   | Description |
|-----------------------------|-------------|
| `LUCCA_UNIT_TEST_DEP_CODE`  | Department code used in unit tests (`null` if unused) |
| `TEST_USERNAME`             | Username used in PHPUnit tests |

---

## 🔁 Logging & Rotation

| Variable              | Description |
|------------------------|-------------|
| `MAX_ROTATING_FILES`   | Max number of rotated log files (0 = infinite) |

---

## 🖨️ PDF Generation (KNP Snappy)

| Variable               | Description |
|-------------------------|-------------|
| `WKHTMLTOPDF_PATH`      | Path to `wkhtmltopdf` binary (in container) |
| `WKHTMLTOIMAGE_PATH`    | Path to `wkhtmltoimage` binary |

---

## 💡 Notes

- All variables can be set via `.env`, `.env.local`, or injected via Docker in `docker-compose.yml`.
- In CI/CD (GitLab/GitHub), values should be injected as protected or secret environment variables.
- For local development, you can create a `.env.docker` to separate dev values from production.

---

## 🧩 Symfony Path Placeholders

Some variables use Symfony-specific placeholders such as `%kernel.cache_dir%` or `%kernel.project_dir%`.  
These will be automatically resolved by the Symfony container at runtime.

| Placeholder              | Description |
|--------------------------|-------------|
| `%kernel.project_dir%`   | Absolute path to the root of the Symfony project (where `composer.json` is) |
| `%kernel.cache_dir%`     | Path to the cache directory (e.g. `var/cache/dev` or `var/cache/prod`) |

> 💡 These are mostly used for defining temporary folders (PDF generation, uploads, etc.) and should not be modified directly unless you know what you're doing.

