# Custom Pages

[![Test Status](https://github.com/humhub/humhub-modules-custom-pages/workflows/PHP%20Codeception%20Tests/badge.svg)](https://github.com/humhub/humhub-modules-custom-pages/actions)

The custom pages modules allows the creation of customized **pages** and **snippets** (sidebar elements) as well as on space and on 
global level. 

Depending on content type and target the following content types are available:

- MarkDown (HumHub Markdown Richtext based page)
- Link (External link)
- Iframe
- Template
- Html (Only available for global pages for security reasons)
- PHP (requires further activation under `Administration -> Custom Pages -> Settings`)

By default the module supports the following **targets** for global pages:

- Top Navigation
- User Account Menu (Account Settings)
- Directory Menu
- No Specific target (Direct link)

and global Snippets:

- Dashboard
- Directory

On space level the following page targets are supported by default:

- Space Navigation

and snippets:

- Stream Sidebar