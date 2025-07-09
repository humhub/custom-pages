
## Elements

| Variable Name                      | Data Type                   | Description                                                                     |
|------------------------------------|-----------------------------|---------------------------------------------------------------------------------|
| **Generic element attributes**     |                             | Applies to all variables                                                        |
| `*.elementContentId`               | Integer                     | Unique ID of element content instance                                           |
| `*.empty`                          | Boolean                     | Whether the variable is empty or not                                            |
|                                    |                             |                                                                                 |
| **Text Element**                   |                             |                                                                                 |
| `text`                             | String                      | Returns the value of the Text Element.                                          |
|                                    |                             |                                                                                 |
| **File Element**                   |                             |                                                                                 |
| `file`                             | String                      | File URL                                                                        |
| `file.guid`                        | String                      | File unique ID, e.g. 5b12f367-744b-4d2b-9611-c8b4ff92b6e5                       |
| `file.name`                        | String                      | File name                                                                       |
| `file.mimeType`                    | String                      | File mime type                                                                  |
| `file.size`                        | Integer                     | File size in bytes                                                              |
|                                    |                             |                                                                                 |
| **Image Element**                  | Extended from File Element  | It has the same options as the File Element and additional below                |
| `image`                            | String                      | Returns the URL of the value of the Image Element                               |
| `image.src`                        | String                      | Image URL                                                                       |
| `image.alt`                        | String                      | Image alt text                                                                  |
| `image.width`                      | String                      | Image width                                                                     |
| `image.height`                     | String                      | Image height                                                                    |
| `image.style`                      | String                      | Image style                                                                     |
|                                    |                             |                                                                                 |
| **Container**                      |                             | The                                                                             |
| `container`                        | String                      | Returns the URL of the value of the Image Element                               |
|                                    |                             |                                                                                 |
| **Content Container Meta Element** |                             | Base for `User` or `Space` Elements                                             |
| `contentcontainer.guid`            | String                      | Container unique ID, e.g. 5b12f367-744b-4d2b-9611-c8b4ff92b6e5                  |
| `contentcontainer.displayName`     | String                      | The Display name of the container (e.g. First name and last name)               |
| `contentcontainer.displayNameSub`  | String                      |                                                                                 |
| `contentcontainer.url`             | String                      | URL to the container (e.g. Profile URL)                                         |
| `contentcontainer.imageUrl`        | String                      | URL to main space/profile image                                                 |
| `contentcontainer.bannerImageUrl`  | String                      | URL to banner space/profile image                                               |
| `contentcontainer.tags`            | String[]                    | Tags                                                                            |
|                                    |                             |                                                                                 |
| **Spaces Element**                 |                             |                                                                                 |
| `spaces`                           | Space[] > Containers        | Iterable, returns Space variables                                               |
|                                    |                             |                                                                                 |
| **Space Element**                  |                             |                                                                                 |
| `space`                            | Space > Container           |                                                                                 |
| `space.memberCount`                | Integer                     | Returns the number of members of the Space                                      |
|                                    |                             |                                                                                 |
| **Users Element**                  |                             |                                                                                 |
| `users`                            | User[] > Containers         | Iterable, returns User variables                                                |
|                                    |                             |                                                                                 |
| **User Element**                   |                             |                                                                                 |
| `user`                             | User > Container            |                                                                                 |
| `user.friendCount`                 | Integer                     | Returns the number of Friends of the user                                       |
|                                    |                             |                                                                                 |
| **Content Meta Element**           |                             | Base for all content variables (e.g. Post)                                      |
| `content.url`                      | String                      | Permalink to the content record                                                 |
| `content.guid`                     | String                      | Content unique ID, e.g. 5b12f367-744b-4d2b-9611-c8b4ff92b6e5                    |
| `content.container`                | Container                   |                                                                                 |
| `content.isPublished`              | Boolean                     | True if the content is published                                                |
| `content.isArchived`               | Boolean                     | True if the content is archived                                                 |
| `content.isDraft`                  | Boolean                     | True if the content is a draft                                                  |
| `content.isPinned`                 | Boolean                     | True if the content is pinned                                                   |
| `content.isPublic`                 | Boolean                     | True if it is a Public content                                                  |
| `content.isPrivate`                | Boolean                     | True if it is a Private content                                                 |
| `content.isHidden`                 | Boolean                     | True if it is a Hidden content                                                  |
| `content.lockedComments`           | Boolean                     | True if comments are locked                                                     |
| `content.createdBy`                | User                        | User element                                                                    |
| `content.createdAt`                | String                      | Format: 2025-01-02 16:45:00                                                     |
| `content.updatedBy`                | User                        | User element                                                                    |
| `content.updatedAt`                | String                      | Format: 2025-01-02 16:45:00                                                     |
|                                    |                             |                                                                                 |
| **Post Element**                   |                             |                                                                                 |
| `post `                            | String > Content            | Text of the Post                                                                |
|                                    |                             |                                                                                 |
| **RSS Element**                    |                             |                                                                                 |
| `rss`                              | Iterable                    |                                                                                 |
|                                    |                             |                                                                                 |
| **HTML Element**                   |                             |                                                                                 |
| `html`                             |                             | The raw HTML output of the HTML Element                                         |
|                                    |                             |                                                                                 |
| **Markdown Element**               |                             |                                                                                 |
| `markdown`                         | String                      | The Markdown output as HTML of the Markdown element                             |
| `markdown.raw`                     | String                      | Raw Markdown code of the element                                                |
|                                    |                             |                                                                                 |
| **Calendars Element**              |                             |                                                                                 |
| `calendars`                        | CalendarEntry[]             | Iterable of selected Calendar Entries                                           |
|                                    |                             |                                                                                 |
| **Calendar Entry Element**         |                             |                                                                                 |
| `calendar`                         | CalendarEntry > Content     |                                                                                 |
| `calendar.title`                   | String                      | Title                                                                           |
| `calendar.description`             | String                      | Markdown Description                                                            |
| `calendar.url`                     | String                      | Relative URL to the Calendar Entry                                              |
| `calendar.color`                   | String                      | Hex Color Code                                                                  |
| `calendar.startDateTime `          | String                      | Format: 2025-01-02 16:45:00                                                     |
| `calendar.endDateTime`             | String                      | Format: 2025-01-02 22:15:00                                                     |
| `calendar.location`                | String                      | Location of the event                                                           |
|                                    |                             |                                                                                 |
| **Files (Module) Element**         |                             |                                                                                 |
| `files`                            | File[]                      | Iterable of selected Files                                                      |
|                                    |                             |                                                                                 |
| **File (Module) Element**          |                             |                                                                                 |
| `file`                             | File > Url or Description   |                                                                                 |
| `file.description`                 | String                      | File description                                                                |
| `file.downloadCount`               | Integer                     | Number of downloads                                                             |
| `file.fileUrl`                     | String                      | File URL                                                                        |
| `file.icon`                        | String                      | Icon style class, e.g. image: `fa-file-image-o`, pdf: `fa-file-pdf-o`           |
| `file.file`                        | File Element                | Base/core File element                                                          |
|                                    |                             |                                                                                 |
| **Folders Element**                |                             |                                                                                 |
| `folders`                          | Folder[]                    | Iterable of selected Folders                                                    |
|                                    |                             |                                                                                 |
| **Folder Element**                 |                             |                                                                                 |
| `folder`                           | Folder > Url or Description |                                                                                 |
| `folder.title`                     | String                      | Folder title                                                                    |
| `folder.description`               | String                      | Folder description                                                              |
| `folder.type`                      | String                      | Type: 'posted' - Files from the stream, 'root' - Root folder, null - sub folder |
| `folder.icon`                      | String                      | Icon style class: `fa-folder`                                                   |
| `folder.subFolders`                | Folder[]                    | Sub folder elements                                                             |
| `folder.subFiles`                  | File[]                      | Sub file elements                                                               |
|                                    |                             |                                                                                 |
| **News list Element**              |                             |                                                                                 |
| `newsList`                         | News[]                      | Iterable of selected news                                                       |
|                                    |                             |                                                                                 |
| **News Element**                   |                             |                                                                                 |
| `news`                             | News > Title                |                                                                                 |
| `news.title`                       | String                      | News title                                                                      |
| `news.article`                     | String                      | News article                                                                    |
| `news.canBeConfirmed`              | Boolean                     | True when a read confirmation is required                                       |
| `news.isConfirmed`                 | Boolean                     | True when current user have read the news                                       |
| `news.confirmedMembersCount`       | Integer                     | Number of users who read the news                                               |
| `news.confirmedReadingPercent`     | Integer                     | Percent of users who read the news (0 - 100)                                    |
|                                    |                             |                                                                                 |
| **Polls Element**                  |                             |                                                                                 |
| `polls`                            | Poll[]                      | Iterable of selected Polls                                                      |
|                                    |                             |                                                                                 |
| **Poll Element**                   |                             |                                                                                 |
| `poll`                             | Poll > Question             |                                                                                 |
| `poll.question`                    | String                      | Poll question message                                                           |
| `poll.description`                 | String                      | Poll description                                                                |
| `poll.closed`                      | Boolean                     | True if the poll is closed                                                      |
| `poll.answers`                     | Answers[]                   | Iterable of answers                                                             |
|                                    |                             |                                                                                 |
| **Poll Answer**                    |                             |                                                                                 |
| `poll.answers[i].answer`           | String                      | Answer text                                                                     |
| `poll.answers[i].votes`            | Integer                     | Number of votes                                                                 |
|                                    |                             |                                                                                 |
| **Tasks Element**                  |                             |                                                                                 |
| `tasks`                            | Task[]                      | Iterable of selected Tasks                                                      |
|                                    |                             |                                                                                 |
| **Task Element**                   |                             |                                                                                 |
| `task`                             | Task > Title                |                                                                                 |
| `task.title`                       | String                      | Title                                                                           |
| `task.description`                 | String                      | Description                                                                     |
| `task.isScheduled`                 | Boolean                     | True if the scheduling is enabled for the task                                  |
| `task.allDay`                      | Boolean                     | True if the scheduling is set to full day                                       |
| `task.startDateTime`               | String                      | Format: 2025-01-02 16:45:00                                                     |
| `task.endDateTime`                 | String                      | Format: 2025-01-02 19:25:00                                                     |
| `task.isAddedToCalendar`           | Boolean                     | True if the task is added to Space/Profile calendar                             |
| `task.timeZone`                    | String                      | Time zone, e.g. Europe/Berlin                                                   |
| `task.listName`                    | String                      | Task list name                                                                  |
| `task.listColor`                   | String                      | Task list color                                                                 |
| `task.checkPoints`                 | TaskCheckPoints[]           | Iterable of task check points                                                   |
| `task.assignedUsers`               | User[]                      | Iterable of assigned users                                                      |
| `task.responsibleUsers`            | User[]                      | Iterable of responsible users                                                   |
| `task.isReviewRequired`            | Boolean                     | True if a review by responsible user required                                   |
|                                    |                             |                                                                                 |
| **Task Check Point**               |                             |                                                                                 |
| `task.checkPoints[i].title`        | String                      | Check point title                                                               |
| `task.checkPoints[i].description`  | String                      | Check point description                                                         |
| `task.checkPoints[i].completed`    | Boolean                     | True if the check point is completed                                            |
| `task.checkPoints[i].sortOrder`    | Integer                     | Check point sort order                                                          |
|                                    |                             |                                                                                 |
| **Wiki Pages Element**             |                             |                                                                                 |
| `wikis`                            | WikiPage[]                  | Iterable of selected Wiki Pages                                                 |
|                                    |                             |                                                                                 |
| **Wiki Page Element**              |                             |                                                                                 |
| `wiki`                             | WikiPage > Content          |                                                                                 |
| `wiki.title`                       | String                      | Wiki page title                                                                 |
| `wiki.content`                     | String                      | Latest Wiki revision (Markdown)                                                 |
| `wiki.isHome`                      | Boolean                     | Is home page                                                                    |
| `wiki.isAdminOnly`                 | Boolean                     | Protected page (only for admin)                                                 |
| `wiki.sortOrder `                  | Integer                     | Sort order in list                                                              |
| `wiki.isContainerMenu`             | Boolean                     | Show the wiki page in Space/Profile menu                                        |
| `wiki.containerMenuOrder`          | Integer                     | Sort order in Space/Profile menu                                                |
