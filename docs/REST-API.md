# Custom Pages REST API

Readonly REST API for custom pages

## Visibility

All endpoints respect the page's own visibility settings (Public / Logged-In Users / Non-Logged-In Users / Administrative Users / Custom group- or language-restricted), the same rules that apply when viewing the page in the web UI. A page returns `403` on `/page/{id}` when the requesting user is not allowed to view it, and is silently omitted from the list endpoints. Users with the `Manage Pages` permission can always view all pages.

This visibility check is applied after the database-level pagination, so a listing page can come back with fewer results than `limit` (or, rarely, empty) when restricted pages are mixed into it; `total`/`pages` count all matching pages before this filter and may likewise be slightly higher than what a user with restricted access will actually see across all pages combined.

## Endpoints

### Get all Custom Pages

- Method: GET
- Path: `/api/v1/custom-pages`
- Global Custom Pages: `/api/v1/custom-pages?contentcontainer_id=0`
- Alias for global Custom Pages: `/api/v1/custom-pages/global`

### Get Custom Pages by container

- Method: GET
- Path: `/api/v1/custom-pages/container/{containerId}`

### Get a specific Custom Page

- Method: GET
- Path: `/api/v1/custom-pages/page/{id}`

## Example-Request

```bash
curl -H "Authorization: Bearer <TOKEN>" \
  "https://example.org/api/v1/custom-pages/page/123"
```

## Example-Response

```json
{
  "id": 123,
  "title": "My custom page",
  "type": 2,
  "target": "SpaceMenu",
  "icon": "fa-file-text-o",
  "sort_order": 100,
  "in_new_window": false,
  "url": "my-custom-page",
  "abstract": "Short description",
  "cssClass": "team-page",
  "page_content": "# Raw content",
  "rendered_content": "<h1>Rendered content</h1><p>… as displayed…</p>",
  "text_content": "Rendered content… as displayed…",
  "visibility": 1,
  "hide_menu": false,
  "page_type": "page",
  "template_id": 0,
  "page_url": "https://example.org/s/space/custom_pages/view?id=123",
  "permalink": "https://example.org/content/perma?id=999",
  "content": {
    "id": 999,
    "guid": "…",
    "created_at": "2026-08-31 10:00:00"
  }
}
```

