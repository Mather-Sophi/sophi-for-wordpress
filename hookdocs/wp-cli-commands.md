### Sync content to Sophi Collector

`$ wp sophi sync [--post_types=<string>] [--limit=<number>] [--per_page=<number>] [--include=<number>]`

Sync all supported content to Sophi Collector, firing off update events for all of them.  The expected use case with the Sophi for WordPress plugin is that someone will install it on an existing site and instead of having to manually update each piece of content to ensure that it makes it to the Collector, they can run this script to handle that all at once.

#### Options

**`--post_types=<string>`**

Post types to be processed. Comma separated for passing multiple post types.

default: `false`
options:
- any post type name
- `false`

**`--limit=<number>`**

Limit the amount of posts to be synced.

default: `false`
options:
- `false`, no limit
- `N`, max number of posts to sync

**`--per_page=<number>`**

Number of posts to process each batch.

default: `false`
options:
- `false`, no limit
- `N`, max number of posts to sync each batch

**`--include=<number>`**

Post IDs to process. Comma separated for passing multiple item.

default: `false`
options:
- `false`, no limit
- `N`, Post IDs to sync
