Changelog
=========

1.11.0-beta.8 (Unreleased)
-----------------------------
- Enh #432: Add Yii formatter for Twig filters
- Enh #437: Add context menu to Structure View

1.11.0-beta.7 (July 4, 2025)
-----------------------------
- Fix #429: Enable structure view on snippet template inline editing
- Enh #431: Use content ID for Content Record Elements

1.11.0-beta.6 (June 30, 2025)
-----------------------------
- Fix #426: Content record ID fields for template elements

1.11.0-beta.5 (June 27, 2025)
-----------------------------
- Fix #425: Fix image element variable properties type

1.11.0-beta.4 (June 24, 2025)
-----------------------------
- Enh #415: Display "Edit template" menu on structure view
- Enh #417: Allow templates with option "Allow in spaces" also for global pages
- Enh #419: Allow to change category of custom page
- Enh #420: Copy custom page
- Enh #421: Rename RichText elements

1.11.0-beta.3 (June 3, 2025)
----------------------------
Please note that the IDs of the custom pages you have created will be changed. Links may need to be updated.

- Enh #354: Template - Add RSS Element Type
- Enh #357: Refactor all snippets and container pages to single page class
- Enh #358: Refactor page content types
- Enh #359: Template - Add Space Element Type
- Enh #360: Template - Spaces and Users Element Type
- Enh #362: Template - Rendering & Caching
- Fix #365: Fix allowed properties for Twig v3.14.1+
- Enh #367: Migrate all element contents to single table
- Fix #376: Use template name for allowed templates in the container element dynamic attributes
- Fix #377: Rename "snipped" to "snippet"
- Enh #378: Refactor template instance and owner content
- Enh #387: Remove inline editing of container item elements
- Enh #396: Structure overview
- Enh #399: Remove deprecated widget `DataSaved`
- Enh #404: Copy templates
- Enh #406: Twig extension to process markdown text
- Enh #410: Allow to export/import whole container

1.10.16 (Unreleased)
---------------------------
- Fix #400: Fix page loading by url shortcut

1.10.15 (February 11, 2025)
---------------------------
- Fix #391: Fix image view permission from sub container

1.10.14 (January 30, 2025)
--------------------------
- Fix #379: Fix visibility of attached images to default content of template

1.10.13 (January 16, 2025)
--------------------------
- Fix #374: Fix allowed properties for Twig v3.14.1+

1.10.12 (January 16, 2025)
--------------------------
- Enh #353: Reduce translation message categories
- Fix #355: Fix visibility of attached images to Template Layout

1.10.10 (October 12, 2024)
--------------------------
- Enh #352: Deferred showing content until Markdown is rendered properly

1.10.9 (October 3, 2024)
------------------------
- Fix #349: Fix visibility of attached images to template page
- Enh #351: Use PHP CS Fixer

1.10.8 (September 24, 2024)
---------------------------
- Fix #347: Fix errors after save a new page
- Enh #308: Deny access for files from template of hidden content

1.10.7 (September 19, 2024)
----------------------------
- Enh #342: JS Dependency Updates
- Enh #344: Replace theme variables with CSS variables
- Fix #346: Disable automatic iframe attribute "sandbox" on TinyMCE editor

1.10.6 (August 6, 2024)
-----------------------
- Fix: Add autofocus on item or element edit (for HumHub 1.17 - see https://github.com/humhub/humhub/issues/7136)
- Fix #339: Fix stream channel for search indexing

1.10.5 (July 15, 2024)
----------------------
- Enh #336: Renaming page and snippet to global and space types
- Fix #337: Ignore cache on content search index building

1.10.4 (June 19, 2024)
----------------------
- Fix #328: Missing Iframe attributes in snippet
- Fix #333: Fix saving of iframe URL by space admin

1.10.3 (May 15, 2024)
---------------------
- Fix #321: Fix indexing of template content for search
- Fix #326: Fix for search indexing

1.10.2 (January 8, 2024)
------------------------
- Fix: Twig SandboxPolicy Function Whitelist broken

1.10.1 (January 8, 2024)
------------------------
- Enh: Added "random" to Twig SandboxPolicy

1.10.0 (January 6, 2024)
------------------------
- Fix: Highlight admin menu entry when "Template" page is active
- Enh: Added Twig Sandbox Extension and restricted Twig templating features by default

1.9.6 (December 12, 2023)
-------------------------
- Fix #312: Highlight the top menu entry if the current URL matches the Target Url of a "Link" custom page
- Enh #314: Add nonce attribute to all JavaScript tags in snippet templates and HTML snippets automatically

1.9.5 (November 16, 2023)
-------------------------
- Enh #303: Fix visibility of the method `Controller::getAccessRules()`
- Enh #305: Add nonce attribute to all JavaScript tags in templates and HTML pages automatically

1.9.4 (October 24, 2023)
------------------------
- Enh #287: Add Support `Custom Pages Without adding to navigation` for Space
- Fix #291: Fix broken URL in email footer
- Fix #293: Initialize module content class
- Enh #301: Tests for `next` version
- Enh #318: Allow resetting of file/image content to default

1.9.3 (June 13, 2023)
---------------------
- Enh #277: Template usage: display content container and state
- Fix #284: Hide files of HTML pages in stream wall entry

1.9.2 (May 17, 2023)
--------------------
- Fix #277: Delete not published linked content on delete template

- 1.9.1 (May 1, 2023)
-------------------
- Fix #274: Hard delete records on disable module
- Fix #265: Fix tests for core v1.14

1.9.0 (March 28, 2023)
-----------------------
- Fix #261: Add list buttons in toolbar of RichText TinyMCE editor
- Fix #263: Conflicts if a page in a content container has the same ID as a global page
- Fix #270: Don't wrap page type
- Fix #271: Fix compatible `AssetVariable::offsetGet()` between different PHP versions

1.8.9 (February 6, 2023)
------------------------
- Fix #259: Rich Text in page editor broken

1.8.8 (February 2, 2023)
------------------------
- Fix #256: Fix richtext on template editor

1.8.7 (January 8, 2023)
-----------------------
- Enh: Translatable Titles
- Fix #254: Fix compatible with PHP 8.1

1.8.6 (December 1, 2022)
------------------------
- Fix #251: Fix view page from template allowed in Space

1.8.5 (October 11, 2022)
-------------------------
- Fix #244: Add markdown-render class to Markdown text for Translator module to work
- Enh #178: Ability to add attributes to iframe
- Fix TypeError while opening Inline Editor

1.8.4 (September 9, 2022)
-------------------------
- Fix #241: Fix access to view HTML pages

1.8.3 (September 7, 2022)
-------------------------
- Fix #238: Allow HTML Pages and Snippets only for global admins

1.8.2 (September 6, 2022)
-------------------------
- Enh #237: Enable anchor feature for TinyMCE editor

1.8.0 (July 28, 2022)
---------------------
- Fix #232: Migrate to Twig 3 and compatible with PHP 8.1

1.7.3 (July 19. 2022)
--------------------
- Fix #229: Fixed attached images in HTML Editor. Improved Placeholder Admin Alignment

1.7.2 (July 12. 2022)
--------------------
- Fix #228: Fix disabled inputs on edit image and links when editor is opened from modal window

1.7.1 (July 12. 2022)
--------------------
- Fix #227: Fix using of `PeopleHeadingButtons` for old versions

1.7.0 (July 7. 2022)
--------------------
- Fix #223: Deleting attached files from template pages was broken
- Enh #218: Allow custom pages in "People" page as buttons

1.6.4 (June 30, 2022)
---------------------
- Fix #220: Allow all HTML tags in TinyMCE editor

1.6.3 (June 29, 2022)
---------------------
- Fix #221: Rendering of TinyMCE on edit richtext element of template

1.6.2 (June 28, 2022)
---------------------
- Fix #217: TinyMCE not loading correctly on new template sites

1.6.1 (June 23, 2022)
---------------------
- Fix #211: Fix duplicated template HTML source field
- Enh #210: Added TinyMCE Editor for HTML Pages
- Enh #210: Switched from CKEditor to TinyMCE
- Enh #210: Also HTML Snippets

1.5.0 (April 14, 2022)
----------------------
- Enh: Use composer CoreMirror asset from core (1.9+)
- Fix #192: Remove "Directory" pages and sidebar widgets
- Enh #195: Deprecate CompatModuleManager
- Enh #200: Removed CActiveForm, CHtml usages.
- Enh #198: Avoid creating notifications on custom page creation
- Enh #196: Footer menu pages
- Fix #191: For iframe pages in a container, better iframe height adjustment

1.4.3 (June 18, 2021)
---------------------
- Fix #187: Fix lost visibility options for snippets

1.4.2 (June 11, 2021)
---------------------
- Enh #163: Attachments for HTML pages
- Enh: HumHub min. version increased to 1.8
- Enh: Improved page edit form with beginCollapsibleFields
- Fix #177: Remove extraneous ?>
- Fix #185: Fix markdown page width on mobile/small screens

1.3.1 (February 23, 2021)
-------------------------
- Fix: Stream Channel was set for AdminOnly pages
- Enh: Add info regarding "Admin Only" pages without Stream support

1.3.0 (February 22, 2021)
-------------------------
- Fix #88: Text alignment resets when reloading ckeditor input
- Chng: Removed ckeditor show more toolbar item due to content filter issues
- Chng: Updated ckeditor to 4.16.0
- Fix #168: Add edit button to markdown pages

1.2.1 (February 05, 2021)
-------------------------
- Fix #166 Only system admin can access custom pages space settings

1.2.0 (December 12, 2020)
-------------------------
- Enh #57: New group permission "Can manage custom pages"
- Fix #57: Deny public/guest access to custom page with type "User Account Menu (Settings)"

1.1.1 (November 12, 2020)
-------------------------
- Fix #151: Avoid error on creating of custom php page with not existing directory from old settings

1.1.0 (November 3, 2020)
------------------------
The default folders for PHP custom pages were changed. New defaults: php-pages/container_pages/, php-pages/container_snippets/, php-pages/global_pages/, php-pages/global_snippets/

- Fix #121: Link color in markdown pages have same color as text
- Fix #143: (Global) PHP pages were lost on module updates
- Chg: Changed HumHub min version to 1.7
- Chg: 1.7 wall stream entry migration
- Fix: Word break and image overflow issue on template page

1.0.10 (September 19, 2020)
--------------------
- Fix #142: Codemirror editor does not initialize correctly when accessing by pjax
- Fix Delete by target error https://github.com/humhub-contrib/blog/issues/3

1.0.9 (July 21, 2020)
--------------------
- Fix #139: Missing `parent::init()` in admin controller throws error in HumHub 1.6.beta1
- Fix #138: Admin only restriction does not include space admin
- Fix #129: Snippet visibility selection to "Public" broken
- Fix #130: Menu item of space page not activated
- Fix #124: one- and two column layout broken on space page
- Fix: Snippet notification content description missing
- Fix #118: Added missing required view file validation
- Fix #120: Invalid edit template link for global snippets

1.0.8 (May 29, 2020)
--------------------
- Fix: Removed redundant twig class loading
- Fix: Custom Page visibility selection to "Public" broken

1.0.7 (April 06, 2020)
--------------------
- Chg: Added 1.5 defer compatibility
- Fix: Fixed "unreachable code after return statement" in humhub.custom_pages.template.TemplateElement.js
- Enh: Improved event handler exception handling

1.0.6 (February 19, 2020)
---------------------
- Fix #113: Double collapse menu item in snippet context menu
- Fix #112: Markdown snippet does not use Richtext output format
- Enh #44: Improved snippet and page visibility

1.0.5 (December 10, 2019)
---------------------
- Fix #105: Global page overview only shows pages created by user (https://github.com/humhub/humhub/issues/3784)
- Fix #107: Open in new window setting ignored

1.0.4 (November 05, 2019)
---------------------
- Fix: Added missing ckeditor plugins

1.0.3 (October 31, 2019)
---------------------
- Fix: Use of wrong content visibility in page migration
- Fix: Guest access for public global pages

1.0.2
---------------------
- Enh: Update Ckeditor to v4.13.0

1.0.1
---------------------
- Enh: 1.4 nonce compatibility

1.0.0  (February 25, 2019)
---------------------
- Enh: Added integration layer
- Enh: New template content type HumHub Richtext
- Enh: Use Markdown Richtext as Markdown editor
- Chng: Aliged CustomContentContainer tables and controller logic
- Enh: Added blank system template
- Chng: Content are public by default if admin_only is not enabled
- Enh: Added codemirror editor
- Enh: Order templates by name
- Fix: onBeforeUnload not working with pjax
- Fix: default data reset throws error
- Enh: Added inline_text flag to text content

0.8.14  (April 5, 2019)
---------------------
- Fix: Missed confirm box on page deletion

0.8.13  (February 25, 2019)
---------------------
- Fixed xss vulnerability issue

0.8.12  (February 05, 2019)
---------------------
- Fix: Space admin can't add template components
- Chng: Updated min version to 1.3.0

0.8.11  (February 01, 2019)
---------------------
- Enh: Updated translations
- Fix #89: XSS vulnerability

0.8.10  (October 27, 2018)
---------------------
- Enh: Translation updates

0.8.9  (October 04, 2018)
---------------------
- Fix: Iframe loader not removed

0.8.8  (September 18, 2018)
---------------------
- Fix: Use of deprecated jQuery load instead of .on('load')

0.8.6  (July 13, 2018)
---------------------
- Fix: added missing custom directories

0.8.5  (July 12, 2018)
---------------------
- Fix: added check for php page active in php file count check
- Fix: edit snippet not working in addition with footer nav

0.8.4  (July 2, 2018)
---------------------
- Fix: PHP 7.2 compatibility issues

0.8.3
-----
- Fix: 1.3 compatibility
- Fix: 1.2.0 compatibility

0.8.2:
- Chg: Use of 'Pages' overview as first admin page

0.8:
- Fix #73: Richtext uploads not attached on initial edit;
- Fix #33: fixed strict access for global pages
- Fix #68: wrong translation key
- Enh: Added page type PHP
- Enh: Added module setting page

0.7.13:
- Fix: Error with FileManager afterSave logic
- Enh: Open FileDownload items in new window

0.7.9:
- Enh: Allow target _blank links in richtext

0.7.8:
- Enh: Added directory menu pages

0.7.7:
- Enh: Added file download content + download item/download list template
- Enh: Usability enhancements
- Fix: Add item with only one allowed template not working
- Enh: Allow Inline Activation only for certain container items
- Enh: Added template elemen title field
- Enh: Use select2 dropdown as template select

0.7.6:
- Fix: Iframe page size fix.

0.7.5:
- Fix: edit snippet issue.
- Fix: icon 'none' in snippet icon selector.
- Fix: Don't show container page in stream.

0.7.4:
- Fix: select2 template selection shrink if rendered in hidden panel
- Fix: Cancel button color
- Fix: Fixed account setting template page container issue
- Enh: #56 Use of select2 dropdown as icon chooser
- Fix: #40 Image/File upload ajax error handling
- Fix: HumHub 1.2.beta.3 support
