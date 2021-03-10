Changelog
=========

1.4.0 (Unreleased)
-------------------------
- Enh #163: Attachments for HTML pages
- Enh: HumHub min. version increased to 1.8
- Enh: Improved page edit form with beginCollapsibleFields


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

