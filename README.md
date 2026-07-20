# Dankcave

Custom WooCommerce theme rebuild for Dankcave.com. Started 2026-07-21.

For project context, scope, and working conventions, see [CLAUDE.md](./CLAUDE.md).

## Folder layout

```
dankcave/
├── CLAUDE.md         # Project brief + working conventions (read first)
├── README.md         # This file
├── design/           # Home-page HTML mockup + reference imagery
├── theme/            # The custom WordPress theme (source of truth)
├── docs/             # VitePress docs site (deferred)
└── notes/            # Working notes, meeting summaries, decisions
```

## Related folders (elsewhere on this machine)

- `/Users/christiandizon/Sites/dankcave-audit/` — original audit report + evidence + extracted UpdraftPlus backup
- `/Users/christiandizon/Sites/dankcave-local/` — Docker WordPress environment for local development

## Quick start (development)

1. Start the Docker mirror: `cd /Users/christiandizon/Sites/dankcave-local && docker compose up -d`
2. Symlink or rsync `theme/` into the container: `docker cp theme/ dankcave-wp:/var/www/html/wp-content/themes/dankcave/`
3. Activate the theme via WP-CLI: `docker exec dankcave-cli wp theme activate dankcave --allow-root`
4. View at http://localhost:8090

## Status

See CLAUDE.md § Current status.
