# Estatein — Local WordPress Development with DDEV

This project runs on [DDEV](https://ddev.com/), a Docker-based local development environment. This README covers how to get the project running on a new machine and the day-to-day tools DDEV gives you for WordPress work.

## Getting Ready

Install [DDEV](https://ddev.com/get-started/) and Docker first — follow the official [DDEV installation docs](https://docs.ddev.com/en/stable/users/install/) for your platform. Windows users should set up WSL2 as the home for their projects and Docker; it's supported as a first-class citizen by most major IDEs now.

DDEV bundles [WP-CLI](https://wp-cli.org/), so it's available immediately once the project starts — no separate install needed.

### Clone and configure

```bash
git clone <repo-url> estatein
cd estatein
```

The DDEV config already lives in this repo at [.ddev/config.yaml](.ddev/config.yaml):

```yaml
name: estatein
type: wordpress
docroot: .
php_version: "8.4"
webserver_type: nginx-fpm
database:
    type: mariadb
    version: "11.8"
nodejs_version: "24"
```

### Start the project

```bash
ddev start
```

First run takes a minute or so while Docker images download. DDEV will let you know when it's ready.

### Get WordPress core

WordPress core files (`wp-admin/`, `wp-includes/`, root `wp-*.php`, etc.) are **not** committed to this repo — they're redownloadable via WP-CLI and only add noise/merge pain:

```bash
ddev wp core download
```

### Install WordPress

Either run `ddev launch` and follow the install wizard in the browser (default DB host/name/user/password are all `db`), or do it entirely from the CLI:

```bash
ddev wp core install \
  --url='https://estatein.ddev.site' \
  --title='Estatein' \
  --admin_user=admin \
  --admin_password=<choose-your-own> \
  --admin_email=<your-email>
```

Once done, the site is live at `https://estatein.ddev.site` with admin at `https://estatein.ddev.site/wp-admin`.

### Import an existing database / uploads (if applicable)

```bash
ddev import-db --file=/path/to/database.sql.gz
ddev import-files --source=/path/to/uploads.tar.gz
```

## What's tracked in this repo

See [.gitignore](.gitignore). In short:

- **Tracked:** `.ddev/`, custom theme(s) and plugin(s) under `wp-content/`, `.gitignore`, this README, `wp-config-sample.php`
- **Ignored:** WordPress core files, default bundled themes/plugins (twentytwenty*, akismet, hello.php), `wp-config.php` (has DB credentials), `wp-content/uploads/`, cache/log files

## Mail Testing with Mailpit

DDEV includes [Mailpit](https://mailpit.axllent.org/) out of the box — a local SMTP server that captures outgoing mail instead of sending it anywhere. WordPress uses `sendmail` by default, and DDEV intercepts this automatically, so any mail sent from WordPress (password resets, notifications, etc.) just shows up — no SMTP plugin required.

If you're using an SMTP library directly, point it at:

- **Host:** `localhost`
- **Port:** `1025`
- **Username/Password:** blank

To view captured mail:

```bash
ddev mailpit
```

## Database Administration with Adminer

For inspecting/debugging plugin-created tables and data, install the Adminer add-on:

```bash
ddev add-on get ddev/ddev-adminer
ddev restart
```

Then launch it any time with:

```bash
ddev adminer
```

It connects straight to the project's default database — no config needed.

## Working on Themes/Plugins Outside the Project

If you're developing a theme or plugin as its own repo (separate from this one), avoid nesting it directly inside `wp-content/themes` or `wp-content/plugins`. Instead, use a Docker bind mount so it lives in its own directory but appears inside the WordPress install.

Create `.ddev/docker-compose.override.yaml`:

```yaml
services:
  web:
    volumes:
      - /absolute/path/to/your-plugin:/var/www/html/wp-content/plugins/your-plugin
```

Then:

```bash
ddev restart
```

Changes made in the external directory are picked up by WordPress immediately, and the plugin/theme repo stays independent of this one (its own git history, build scripts, etc.).

## Useful Commands

| Command | Purpose |
|---|---|
| `ddev start` / `ddev stop` | Start/stop the project containers |
| `ddev describe` | Show project URLs, ports, services |
| `ddev ssh` | Shell into the web container |
| `ddev wp <command>` | Run any WP-CLI command |
| `ddev logs` | Tail container logs |
| `ddev mailpit` | Open captured outgoing mail |
| `ddev adminer` | Open database GUI |
| `ddev export-db` | Export the current database |
