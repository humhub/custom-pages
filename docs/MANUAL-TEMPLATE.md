
## Elements

| Variable Name                       | Data Type               | Description                                                       |
|-------------------------------------|-------------------------|-------------------------------------------------------------------|
| **Generic element attributes**      |                         | Applies to all variables                                          |
| `*.elementContentId`                | Integer                 | Unique ID of element content instance                             |
| `*.empty`                           | Boolean                 | Whether the variable is empty or not                              |
|                                     |                         |                                                                   |
| **Text Element**                    |                         |                                                                   |
| `text`                              | String                  | Returns the value of the Text Element.                            |
|                                     |                         |                                                                   |
| **Image Element**                   |                         |                                                                   |
| `image`                             | String                  | Returns the URL of the value of the Image Element                 |
|                                     |                         |                                                                   |
| **Container**                       |                         | The                                                               |
| `container`                         | String                  | Returns the URL of the value of the Image Element                 |
|                                     |                         |                                                                   |
| **Content Container Meta Element**  |                         | Base for `User` or `Space` Elements                               |
| `contentcontainer.guid`             | String                  |                                                                   |
| `contentcontainer.displayName`      | String                  | The Display name of the container (e.g. First name and last name) |
| `contentcontainer.displayNameSub`   | String                  |                                                                   |
| `contentcontainer.url`              | String                  | URL to the container (e.g. Profile URL)                           |
| `contentcontainer.imageUrl`         | String                  |                                                                   |
| `contentcontainer.tags`             | String[]                |                                                                   |
|                                     |                         |                                                                   |
| **Spaces Element**                  |                         |                                                                   |
| `spaces`                            | Space[] > Containers    | Iterable, returns Space variables                                 |
|                                     |                         |                                                                   |
| **Space Element**                   |                         |                                                                   |
| `space`                             | Space > Container       |                                                                   |
| `space.memberCount`                 | Interger                | Returns the number of members of the Space                        |
|                                     |                         |                                                                   |
| **Users Element**                   |                         |                                                                   |
| `users`                             | User[] > Containers     | Iterable, returns User variables                                  |
|                                     |                         |                                                                   |
| **User Element**                    |                         |                                                                   |
| `user`                              | User > Container        |                                                                   |
| `user.friendCount`                  |                         | Returns the number of Friends of the user                         |
|                                     |                         |                                                                   |
| **Content Meta Element**            |                         | Base for all content variables (e.g. Post)                        |
| `content.url`                       | String                  |                                                                   |
| `content.guid`                      | String                  |                                                                   |
| `content.isPublished`               | Boolean                 |                                                                   |
| `content.isArchived`                | Boolean                 |                                                                   |
| `content.isDraft`                   | Boolean                 |                                                                   |
| `content.isPinned`                  | Boolean                 |                                                                   |
| `content.isPublic`                  | Boolean                 |                                                                   |
| `content.isPrivate`                 | Boolean                 |                                                                   |
| `content.isHidden`                  | Boolean                 |                                                                   |
| `content.lockedComments`            | Boolean                 |                                                                   |
| `content.createdBy`                 | User                    |                                                                   |
| `content.createdAt`                 | String                  |                                                                   |
| `content.updatedBy`                 | User                    |                                                                   |
| `content.updatedAt`                 | String                  |                                                                   |
|                                     |                         |                                                                   |
| **Post Element**                    |                         |                                                                   |
| `post `                             | String > Content        | Text of the Post                                                  |
|                                     |                         |                                                                   |
| **RSS Element**                     |                         |                                                                   |
| `rss`                               | Iterable                |                                                                   |
|                                     |                         |                                                                   |
| **HTML Element**                    |                         |                                                                   |
| `html`                              |                         | The raw HTML output of the HTML Element                           |
|                                     |                         |                                                                   |
| **Markdown Element**                |                         |                                                                   |
| `markdown`                          | String                  | The Markdown output as HTML of the Markdown element               |
| `markdown.raw`                      | String                  | Raw Markdown code of the element                                  |
|                                     |                         |                                                                   |
| **Calendars Element**               |                         |                                                                   |
| `calendars`                         | CalendarEntry[]         | Iterable of selected Calendar Entries                             |
|                                     |                         |                                                                   |
| **Calendar Entry Element**          |                         |                                                                   |
| `calendar`                          | CalendarEntry > Content |                                                                   |
| `calendar.title`                    | String                  |                                                                   |
| `calendar.description`              | String                  | Markdown Description                                              |
| `calendar.url`                      | String                  | Relative URL to the Calendar Entry                                |
| `calendar.color`                    | String                  | Hex Color Code                                                    |
| `calendar.startDateTime `           | String                  |                                                                   |
| `calendar.endDateTime`              | String                  |                                                                   |
| `calendar.location`                 | String                  |                                                                   |
