Changelog
=========

1.8.11 (March 28, 2023)
-----------------------
- Fix #261: Add list buttons in toolbar of RichText TinyMCE editor
- Fix #263: Conflicts if a page in a content container has the same ID as a global page
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
The default folders for PHP custom pages were changed.
New defaults: php-pages/container_pages/, php-pages/container_snippets/, php-pages/global_pages/, php-pages/global_snippets/ 

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
- Fix: select2  template selection shrink if rendered in hidden panel
- Fix: Cancel button color
- Fix: Fixed account setting template page container issue
- Enh: #56 Use of select2 dropdown as icon chooser
- Fix: #40 Image/File upload ajax error handling
- Fix: HumHub 1.2.beta.3 support

