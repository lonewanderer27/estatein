# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress site for Estatein, a real estate marketing site. Custom work lives entirely in two places under `wp-content/`:

- `wp-content/themes/estatein/` — the front-end theme (Bootstrap 5.3.8-based)
- `wp-content/plugins/custom-post-types/` — registers all custom post types and the site-wide contact settings page

Everything else (WordPress core, bundled themes like twentytwenty*, akismet, hello.php) is either not tracked in git or is stock WordPress — don't spend time reading it as "project code."

## Local environment (DDEV)

The project runs on [DDEV](https://ddev.com/). Full setup steps are in [README.md](README.md); the essentials:

```bash
ddev start                 # start containers
ddev wp core download       # WP core isn't tracked in git — fetch it
ddev wp <command>           # any WP-CLI command
ddev ssh                    # shell into the web container
ddev logs                   # tail container logs
ddev mailpit                # view mail WordPress has sent (captured, not delivered)
```

There is no build step, bundler, package.json, or test suite in this repo — theme JS/CSS is hand-written and enqueued directly (no compilation), and Bootstrap is vendored as prebuilt files under `assets/vendor/bootstrap/`. There is nothing to "run tests" against; verification is manual (load the page via `ddev launch`, check `ddev logs` for PHP errors).

WordPress core files, `wp-config.php`, default bundled themes/plugins, and `wp-content/uploads/` are gitignored — see [.gitignore](.gitignore). Only the custom theme, custom plugin, and repo scaffolding are tracked.

## Architecture

### Plugin owns data, theme owns presentation

`custom-post-types` plugin registers every custom post type (`property`, `location`, `testimonial`, `faq`, `value`, `team-member`, `highlight`) plus a single contact-settings options page. Each CPT lives in its own file under `includes/`, required from `custom-post-types.php`. The theme's `functions.php` adds presentational fields on top of the plugin's post types when a field is about display rather than data (e.g. `property_type` for the card badge) rather than editing the plugin.

### Meta box pattern (repeated per field/CPT)

Every custom field in the plugin and theme follows the same four-part shape — grep for one you're modeling a new field on rather than inventing a new pattern:

1. `register_post_meta()` on `init`
2. `add_meta_box()` registered via `add_action( 'add_meta_boxes_{post_type}', ... )`
3. A render callback that outputs the field and a `wp_nonce_field()`
4. A save callback hooked to `save_post_{post_type}` that verifies the nonce, checks `DOING_AUTOSAVE` and `current_user_can( 'edit_post', ... )`, then `update_post_meta()`

Field arrays that back both a meta box render and a save (and sometimes a read helper) are centralized in one function, e.g. `estatein_property_pricing_fields()` in `includes/property.php` — update that one array rather than the render/save functions individually when adding a pricing field.

### Shared `location` CPT

`location` (`includes/location.php`) is a hierarchical (Country > State > City) reference post type other CPTs attach to instead of storing free-text locations. `estatein_register_location_field( $post_type )` wires up the "Location" meta box (a page dropdown) for any post type that needs one — currently `property`; call it the same way to add a location picker to another CPT. `estatein_get_location( $post_id )` reads it back; the theme's `estatein_theme_location_label()` in `functions.php` formats it as `"Parent, Child"`.

### Front page composition

`front-page.php` is a plain sequence of `get_template_part()` calls, one per homepage section, each backed by a file in `template-parts/front-page/`:

```
hero → highlights → featured-properties → testimonials → faq → cta
```

Section templates query their own data (`get_posts()` for the relevant CPT) and pass individual items into `template-parts/components/*.php` via the `get_template_part( $slug, null, $args )` fourth-argument form — the component then reads `$args['<key>']`. Look at `template-parts/front-page/featured-properties.php` + `template-parts/components/property-card.php` as the reference pair before adding a new section or card component.

Variables inside template parts are prefixed `$estatein_...` throughout (not a WordPress convention, but consistent across this codebase) — match it in new template code so `get_template_part`'s variable scope-leak doesn't collide with parent-scope names.

### Assets

`functions.php`'s `estatein_component_stylesheets()` lists component stylesheet names that get enqueued from `assets/css/components/`, each depending on `estatein-base` (tokens, body defaults, shared section/button chrome in `assets/css/base.css`). Adding a new component stylesheet means adding its filename (no extension) to that array *and* creating the matching CSS file — the enqueue is driven entirely by that list. `assets/js/main.js` is the single enqueued script, loaded after the vendored Bootstrap bundle.

### Icons

There's no icon font or SVG sprite build — `estatein_theme_icon( $name )` in `functions.php` echoes inline SVG markup from a hardcoded associative array. Add new icons there rather than pulling in an icon library.
