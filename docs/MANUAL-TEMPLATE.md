
## Elements

| Variable Name                       | Data Type                  | Description                                                       |
|-------------------------------------|----------------------------|-------------------------------------------------------------------|
| **Generic element attributes**      |                            | Applies to all variables                                          |
| `*.elementContentId`                | Integer                    | Unique ID of element content instance                             |
| `*.empty`                           | Boolean                    | Whether the variable is empty or not                              |
|                                     |                            |                                                                   |
| **Text Element**                    |                            |                                                                   |
| `text`                              | String                     | Returns the value of the Text Element.                            |
|                                     |                            |                                                                   |
| **File Element**                    |                            |                                                                   |
| `file`                              | String                     | File URL                                                          |
| `file.guid`                         | String                     | File unique ID                                                    |
| `file.name`                         | String                     | File name                                                         |
| `file.mimeType`                     | String                     | File mime type                                                    |
| `file.size`                         | Integer                    | File size in bytes                                                |
|                                     |                            |                                                                   |
| **Image Element**                   | Extended from File Element | It has the same options as the File Element and additional below  |
| `image`                             | String                     | Returns the URL of the value of the Image Element                 |
| `image.src`                         | String                     | Image URL                                                         |
| `image.alt`                         | String                     | Image alt text                                                    |
| `image.width`                       | String                     | Image width                                                       |
| `image.height`                      | String                     | Image height                                                      |
| `image.style`                       | String                     | Image style                                                       |
|                                     |                            |                                                                   |
| **Container**                       |                            | The                                                               |
| `container`                         | String                     | Returns the URL of the value of the Image Element                 |
|                                     |                            |                                                                   |
| **Content Container Meta Element**  |                            | Base for `User` or `Space` Elements                               |
| `contentcontainer.guid`             | String                     |                                                                   |
| `contentcontainer.displayName`      | String                     | The Display name of the container (e.g. First name and last name) |
| `contentcontainer.displayNameSub`   | String                     |                                                                   |
| `contentcontainer.url`              | String                     | URL to the container (e.g. Profile URL)                           |
| `contentcontainer.imageUrl`         | String                     |                                                                   |
| `contentcontainer.tags`             | String[]                   |                                                                   |
|                                     |                            |                                                                   |
| **Spaces Element**                  |                            |                                                                   |
| `spaces`                            | Space[] > Containers       | Iterable, returns Space variables                                 |
|                                     |                            |                                                                   |
| **Space Element**                   |                            |                                                                   |
| `space`                             | Space > Container          |                                                                   |
| `space.memberCount`                 | Interger                   | Returns the number of members of the Space                        |
|                                     |                            |                                                                   |
| **Users Element**                   |                            |                                                                   |
| `users`                             | User[] > Containers        | Iterable, returns User variables                                  |
|                                     |                            |                                                                   |
| **User Element**                    |                            |                                                                   |
| `user`                              | User > Container           |                                                                   |
| `user.friendCount`                  |                            | Returns the number of Friends of the user                         |
|                                     |                            |                                                                   |
| **Content Meta Element**            |                            | Base for all content variables (e.g. Post)                        |
| `content.url`                       | String                     |                                                                   |
| `content.guid`                      | String                     |                                                                   |
| `content.isPublished`               | Boolean                    |                                                                   |
| `content.isArchived`                | Boolean                    |                                                                   |
| `content.isDraft`                   | Boolean                    |                                                                   |
| `content.isPinned`                  | Boolean                    |                                                                   |
| `content.isPublic`                  | Boolean                    |                                                                   |
| `content.isPrivate`                 | Boolean                    |                                                                   |
| `content.isHidden`                  | Boolean                    |                                                                   |
| `content.lockedComments`            | Boolean                    |                                                                   |
| `content.createdBy`                 | User                       |                                                                   |
| `content.createdAt`                 | String                     |                                                                   |
| `content.updatedBy`                 | User                       |                                                                   |
| `content.updatedAt`                 | String                     |                                                                   |
|                                     |                            |                                                                   |
| **Post Element**                    |                            |                                                                   |
| `post `                             | String > Content           | Text of the Post                                                  |
|                                     |                            |                                                                   |
| **RSS Element**                     |                            |                                                                   |
| `rss`                               | Iterable                   |                                                                   |
|                                     |                            |                                                                   |
| **HTML Element**                    |                            |                                                                   |
| `html`                              |                            | The raw HTML output of the HTML Element                           |
|                                     |                            |                                                                   |
| **Markdown Element**                |                            |                                                                   |
| `markdown`                          | String                     | The Markdown output as HTML of the Markdown element               |
| `markdown.raw`                      | String                     | Raw Markdown code of the element                                  |
|                                     |                            |                                                                   |
| **Calendars Element**               |                            |                                                                   |
| `calendars`                         | CalendarEntry[]            | Iterable of selected Calendar Entries                             |
|                                     |                            |                                                                   |
| **Calendar Entry Element**          |                            |                                                                   |
| `calendar`                          | CalendarEntry > Content    |                                                                   |
| `calendar.title`                    | String                     |                                                                   |
| `calendar.description`              | String                     | Markdown Description                                              |
| `calendar.url`                      | String                     | Relative URL to the Calendar Entry                                |
| `calendar.color`                    | String                     | Hex Color Code                                                    |
| `calendar.startDateTime `           | String                     |                                                                   |
| `calendar.endDateTime`              | String                     |                                                                   |
| `calendar.location`                 | String                     |                                                                   |
|                                     |                            |                                                                   |
| **Files (Module) Element**          |                            |                                                                   |
| `files`                             | File[]                     | Iterable of selected Files                                        |
|                                     |                            |                                                                   |
| **File (Module) Element**           |                            |                                                                   |
| `file`                              | File > Url or Description  |                                                                   |
| `file.description`                  | String                     | File description                                                  |
| `file.downloadCount`                | Integer                    | Number of downloads                                               |
| `file.fileUrl`                      | String                     | File URL                                                          |
| `file.file`                         | File Element               | Base/core File element                                            |
|                                     |                            |                                                                   |
| **News list Element**               |                            |                                                                   |
| `newsList`                          | News[]                     | Iterable of selected news                                         |
|                                     |                            |                                                                   |
| **News Element**                    |                            |                                                                   |
| `news`                              | News > Title               |                                                                   |
| `news.title`                        | String                     | News title                                                        |
| `news.article`                      | String                     | News article                                                      |
| `news.canBeConfirmed`               | Boolean                    | True when a read confirmation is required                         |
| `news.isConfirmed`                  | Boolean                    | True when current user have read the news                         |
| `news.confirmedMembersCount`        | Integer                    | Number of users who read the news                                 |
| `news.confirmedReadingPercent`      | Integer                    | Percent of users who read the news (0 - 100)                      |
|                                     |                            |                                                                   |
| **Polls Element**                   |                            |                                                                   |
| `polls`                             | Poll[]                     | Iterable of selected Polls                                        |
|                                     |                            |                                                                   |
| **Poll Element**                    |                            |                                                                   |
| `poll`                              | Poll > Question            |                                                                   |
| `poll.question`                     | String                     | Poll question message                                             |
| `poll.description`                  | String                     | Poll description                                                  |
| `poll.closed`                       | Boolean                    | True if the poll is closed                                        |
| `poll.answers`                      | Answers[]                  | Iterable of answers                                               |
|                                     |                            |                                                                   |
| **Poll Answer**                     |                            |                                                                   |
| `poll.answers[i].answer`            | String                     | Answer text                                                       |
| `poll.answers[i].votes`             | Integer                    | Number of votes                                                   |
|                                     |                            |                                                                   |
| **Wiki Pages Element**              |                            |                                                                   |
| `wikis`                             | WikiPage[]                 | Iterable of selected Wiki Pages                                   |
|                                     |                            |                                                                   |
| **Wiki Page Element**               |                            |                                                                   |
| `wiki`                              | WikiPage > Content         |                                                                   |
| `wiki.title`                        | String                     | Wiki page title                                                   |
| `wiki.content`                      | String                     | Latest Wiki revision (Markdown)                                   |
| `wiki.isHome`                       | Boolean                    | Is home page                                                      |
| `wiki.isAdminOnly`                  | Boolean                    | Protected page (only for admin)                                   |
| `wiki.sortOrder `                   | Integer                    | Sort order in list                                                |
| `wiki.isContainerMenu`              | Boolean                    | Show the wiki page in Space/Profile menu                          |
| `wiki.containerMenuOrder`           | Integer                    | Sort order in Space/Profile menu                                  |
