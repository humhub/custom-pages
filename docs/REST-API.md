# Custom Pages REST API

Readonly REST API for custom pages

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

