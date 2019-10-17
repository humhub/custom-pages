[![Build Status](https://travis-ci.org/humhub/humhub-modules-custom-pages.svg?branch=master)](https://travis-ci.org/humhub/humhub-modules-custom-pages)

## Description

The custom pages modules allows the creation of customized **pages** and **snippets** as well as on space
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

Since v.1.0 external modules can provide own targets through the **CustomPagesService**.

__Module website:__ <https://github.com/humhub/humhub-modules-custom-pages>    
__Author:__ luke, buddh4    
__Author website:__ [humhub.org](http://humhub.org)    


## Changelog

<https://github.com/humhub/humhub-modules-custom-pages/commits/master>

## Bugtracker

<https://github.com/humhub/humhub-modules-custom-pages/issues>

## Getting Started

Global pages and snippets can be maintained by administrators in `Administration -> Custom Pages -> Overview`.

Space pages and snippets require additional module installation on space level and can be maintained by space
administrators in `Space Settings Dropdown -> Custom Pages`.

After selecting a **target** for your new page as for example the **Top Navigation**, you have to select a content type. 
On space level the available content types are limited for security reasons.

Most content types provide the following setting which may vary between different targets:

|  Setting | Description  |
|---|---|
| Title  | Page link or Snippet title  |
| Page Content or url  | E.g. Iframe link or Markdown content  |
| Url shortcut  | If your installation supports pretty urls, this page setting can be used to create page urls  as `www.example.de/p/mypage`  |
| Icon  | Page icons are used for menu link. Snippet icons are displayed beside the title in the snippet head |
| Sort Order  | Used for ordering your sidebar or navigation |
| Style Class  | This class will be added to the root of your page or snippet |
| Only visible for admins  | The page should only be displayed for system or space admin users, this can also be used while concepting a page |
| Open in new window  | This setting can be used for pages in order to open the page in a new window (tab) |

## Templates

The custom pages module provides a simple template mechanism based on [twig](https://twig.symfony.com/).
Templates can be maintained under `Administration -> Templates`.

> Note: In order to use template based pages or snippets on space level, you'll have to allow the layout for spaces within the general settings
of the template.

### Layouts

When creating a new template based page or snippet, you have to select a layout template which is used as the base layout of your page.
Usually a layout will consist of static elements as for example a headline, subheadline and container elements.

You can add the following static elements to your layout:

|  Type | Description  |
|---|---|
| Text  | A simple plain text element |
| Richtext  | A Html richtext editor based on [CKEditor](https://ckeditor.com/) |
| HumHub Richtext  | The HumHub Richtext, can be used for mentionings, oembed etc. |
| Image  | Image element |
| File  | Renders a file url (no link etc) |
| File Download  | Renders a file download link |

A very simple layout could look like the following:

```
<style>
 // Some additional styling 
<style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ headline }}
            </div>
            <div class="panel-body">
                <div class="abstract">
                    {{ abstract }}
                </div>
                <div class="abstract">
                    {{ content }}
                </div>
            </div>
        </div>
    </div>
</div>
```

With a simple text element as **headline**, a HumHub Richtext as **abstract** and a **container** element which allows
multiple container items.

### Container

A container element can contain one or multiple container items. Container items themselves are based on templates managed under
`Administration -> Tempaltes -> Container`. 
A container element can either act as container of **inline** or **block** elements or just allow a single element.
Furthermore a container element can restrict the allowed types of container items.

The following example shows a simple quotation container element with two text elements **text** and **info**:

```
<blockquote>
    {{ text }}
</blockquote>
<span>{{ info }}</span>
```

Another container example with a figure and floating text based upon a **image** image element and **figcaption** text element:

```
<figure class="pull-left">
    {{ image }}
</figure>
<figcaption>{{ figcaption }}</figcaption>

<p> {{ text }}</p>
```











