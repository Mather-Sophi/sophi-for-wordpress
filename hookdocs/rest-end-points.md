The Sophi plugin supports several WP REST Endpoints to perform specific actions on the curated posts.

Right now, there are the following REST Endpoints available in the plugin:

1. Get the Curator Posts
2. Override the curator Posts

### 1. Get the Curator Posts

To get the curators posts from the Sophi API, the following rest endpoint can be used:

### [GET] `/wp-json/sophi/v1/site-automation`

It accepts the following parameters.

|Parameter|Required|Type|Default|
|---|---|---|---|
|pageName|yes|string||
|widgetName|yes|string||
|displayPostExcept|no|string|''|
|displayAuthor|no|string|''|
|displayPostDate|no|string|''|
|displayFeaturedImage|no|string|''|

### Example Request

```text
{WPSiteURL}/wp-json/sophi/v1/site-automation?pageName=test_page&widgetName=test_widget&displayFeaturedImage=true
```

Where `{WPSiteURL}` is a URL of your WordPress site.

### Example Response

```json
[
  {
	"ID": 5859,
	"post_author": "1",
	"post_date": "2022-09-20 15:09:39",
	"post_date_gmt": "2022-09-20 09:39:39",
	"post_content": "<!-- wp:paragraph -->\n<p>Lorem ipsum</p>\n<!-- /wp:paragraph -->",
	"post_title": "Test 01",
	"post_excerpt": "",
	"post_status": "publish",
	"comment_status": "open",
	"ping_status": "open",
	"post_password": "",
	"post_name": "test-01",
	"to_ping": "",
	"pinged": "",
	"post_modified": "2022-10-04 15:39:21",
	"post_modified_gmt": "2022-10-04 10:09:21",
	"post_content_filtered": "",
	"post_parent": 0,
	"guid": "https://wpne.local/?p=5859",
	"menu_order": 0,
	"post_type": "post",
	"post_mime_type": "",
	"comment_count": "0",
	"filter": "raw",
	"postLink": "https://wpne.local/test-01/",
	"featuredImage": "<img width=\"591\" height=\"590\" src=\"https://wpne.local/wp-content/uploads/2022/05/test-01.jpg\" class=\"attachment-post-thumbnail size-post-thumbnail wp-post-image\" alt=\"\" loading=\"lazy\" srcset=\"https://wpne.local/wp-content/uploads/2022/05/test-01.jpg 591w, https://wpne.local/wp-content/uploads/2022/05/test-01-450x450.jpg 450w, https://wpne.local/wp-content/uploads/2022/05/test-01-100x100.jpg 100w, https://wpne.local/wp-content/uploads/2022/05/test-01-300x300.jpg 300w, https://wpne.local/wp-content/uploads/2022/05/test-01-150x150.jpg 150w\" sizes=\"(max-width: 591px) 100vw, 591px\" style=\"width:100%;height:99.83%;max-width:591px;\" />"
  },
  {
	"ID": 5856,
	"post_author": "1",
	"post_date": "2022-09-20 15:00:48",
	"post_date_gmt": "2022-09-20 09:30:48",
	"post_content": "<!-- wp:paragraph -->\n<p>Lorem ipsum</p>\n<!-- /wp:paragraph -->",
	"post_title": "test-02",
	"post_excerpt": "",
	"post_status": "publish",
	"comment_status": "open",
	"ping_status": "open",
	"post_password": "",
	"post_name": "test-02",
	"to_ping": "",
	"pinged": "",
	"post_modified": "2022-09-20 17:00:51",
	"post_modified_gmt": "2022-09-20 11:30:51",
	"post_content_filtered": "",
	"post_parent": 0,
	"guid": "https://wpne.local/?p=5856",
	"menu_order": 0,
	"post_type": "post",
	"post_mime_type": "",
	"comment_count": "0",
	"filter": "raw",
	"postLink": "https://wpne.local/test-02/",
	"featuredImage": ""
  }
]
```

### 2. Override the curator Posts

To override the curators post, the following rest endpoint can be used:

### [POST] `/wp-json/sophi/v1/site-automation-override`

It accepts the following parameters.

|Parameter|Required|Type|Default|
|---|---|---|---|
|ruleType|yes|string|Default is not set<br>Acceptable values:<br>`in/replace/remove/ban`|
|overridePostID|yes|integer||
|pageName|yes|string||
|widgetName|no|string||
|position|no|integer|1|
|overrideExpiry|no|integer|2|

### Example Request

```text
{WPSiteURL}/wp-json/sophi/v1/site-automation-override?pageName=test_name&widgetName=test_widget&ruleType=ban&overridePostID=123&position=1&overrideExpiry=2
```

Where `{WPSiteURL}` is a URL of your WordPress site.

### Example Response

```json
{
  "id": "1085",
  "articleId": "123",
  "widgetName": null,
  "page": "test_name",
  "position": null,
  "requestedUserName": "admin@gmail.com",
  "expirationHour": 2,
  "expirationDate": "2022-10-07T15:34:33.712+00:00",
  "creationDate": "2022-10-07T13:34:33.712+00:00",
  "ruleType": "out",
  "replacedArticleId": null,
  "tenantId": "10up",
  "hostConfigId": "34",
  "status": "ACTIVE"
}
```

### A few error response examples:

When a required parameter is missing in the request.

```json
{
  "code": "rest_missing_callback_param",
  "message": "Missing parameter(s): pageName",
  "data": {
	"status": 400,
	"params": [
	  "pageName"
	]
  }
}
```

When invalid credentials provided. An auth token is generated from the credentials details saved in the WordPress settings page. The error response is received when a token is not generated or invalid.

```json
{
  "code": 401,
  "message": "Invalid API token, please try adding correct credentials on the settings page.",
  "data": null
}
```

When WordPress user is not logged in. Basic Authentication is required.

```json
{
  "code": 401,
  "message": "Unauthorised user, please log in.",
  "data": null
}
```

On unauthorised request.

```json
{
  "code": 401,
  "message": "Unauthorized",
  "data": null
}
```
