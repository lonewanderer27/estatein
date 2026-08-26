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

## Development Process

This site was built as a custom WordPress theme and plugin pair rather than starting from a page builder or a purchased theme, so every piece of markup, styling, and data model is intentional and traceable in git history rather than inherited from third-party scaffolding.

The build proceeded in three broad passes, visible in the commit history:

1. **Data layer first.** The `custom-post-types` plugin was built out before most of the theme — `property`, `location`, `testimonial`, `faq`, `value`, `team-member`, `highlight`, and the lead-capture types (`inquiry`, `property-request`) were registered with their meta boxes and save/read helpers so that template work always had real, structured content to query against rather than placeholder text.
2. **Theme shell and homepage.** The theme was scaffolded (`header.php`, `footer.php`, `functions.php`, design tokens) and the homepage was assembled section-by-section (hero, highlights, featured properties, testimonials, FAQ, CTA), each section as its own template part backed by real CPT queries.
3. **Secondary templates.** The property archive (with filters and a lead-request form), single property page, and services page were layered on afterward, reusing the same component patterns established on the homepage (e.g. the properties carousel and card components) instead of introducing new ones.

Each feature was committed as a focused, scoped change (see `git log --oneline`) — one CPT, one section, or one component per commit — rather than large batched commits, which keeps the history usable as documentation of *why* a given file exists.

## Theme Development Choices

A few deliberate decisions shape how the theme is built, documented in full in [CLAUDE.md](CLAUDE.md):

- **No build step.** There's no `package.json`, bundler, or compile step for the theme. CSS and JS are hand-written and enqueued directly. This keeps the contributor loop to "edit a file, reload the browser" with nothing to install or watch, at the cost of not having Sass, autoprefixing, or minification — an acceptable trade for a site this size.
- **Bootstrap vendored, not installed as a dependency.** Bootstrap 5.3.8's prebuilt CSS/JS is committed under `assets/vendor/bootstrap/` rather than pulled in via npm or a CDN, so the theme has no runtime dependency on an external registry or network availability and behaves identically in every environment.
- **Component-scoped stylesheets over one big stylesheet.** `assets/css/base.css` holds shared tokens, body defaults, and common section/button chrome; everything else is a small, single-purpose stylesheet per component (`property-card.css`, `hero.css`, etc.), enqueued from an explicit list in `functions.php`. This keeps styles easy to locate and delete when a component is removed, instead of accumulating dead rules in a monolithic file.
- **Plugin owns data, theme owns presentation.** Custom post types, their fields, and their storage live entirely in the `custom-post-types` plugin so the data model survives a theme change. The theme only adds fields that are purely about display (e.g. a card badge), never data that belongs in the plugin.
- **One hand-rolled meta box pattern, reused everywhere.** Every custom field (in both the plugin and the theme) follows the same four-part shape — `register_post_meta`, `add_meta_box`, a render callback with a nonce, and a save callback that verifies the nonce and capability before `update_post_meta()`. No third-party fields plugin (e.g. ACF) was introduced, keeping the data layer dependency-free and every field's behavior auditable in plain PHP.
- **A shared, hierarchical `location` CPT** instead of free-text location fields on each post type, so a Country/State/City reference only has to be maintained once and any CPT can attach to it via `estatein_register_location_field()`.
- **Template parts take explicit args**, not implicit globals — section templates query their own data and pass individual items into `template-parts/components/*.php` via `get_template_part()`'s fourth argument, with all template-part variables prefixed `$estatein_...` to avoid WordPress's variable scope-leak causing collisions between a parent template and the part it includes.
- **No icon library.** Icons are inline SVG strings returned by `estatein_theme_icon( $name )` in `functions.php` rather than an icon font or SVG sprite build, avoiding an extra asset pipeline for a small, fixed icon set.

## Plugins & Tools

- **`custom-post-types` (custom, this repo)** — the only functional plugin; registers all CPTs, their meta boxes, and the site-wide contact settings page. No third-party functional plugins (form builders, fields plugins, SEO plugins, etc.) are used — form handling (inquiries, property requests) and SEO basics are hand-rolled in the plugin/theme.
- **Bootstrap 5.3.8** — vendored front-end CSS/JS framework the theme's markup and components are built on top of.
- **DDEV** — local Docker-based WordPress environment (see [Getting Ready](#getting-ready) above); bundles WP-CLI, Mailpit, and PHP/MariaDB/nginx so no local PHP or MySQL install is needed.
- **Mailpit** (bundled with DDEV) — captures outgoing WordPress mail locally instead of sending it, for testing password resets, inquiry notifications, etc.
- **Adminer** (DDEV add-on) — optional database GUI for inspecting CPT data and plugin tables.
- **WP-CLI** (bundled with DDEV) — used for WordPress core install/download and any ad-hoc admin tasks (`ddev wp <command>`).
