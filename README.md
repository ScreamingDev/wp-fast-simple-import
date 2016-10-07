# Easy import for WordPress

Writing an import couldn't be easier.
Just fetch data and forward it to WP-FSI:

    $query = 'SELECT
                uid   AS _import_uid,
                title AS name
              FROM `typo3-db`.categories
              WHERE deleted = 0';
    
    foreach ( fsi_query( $query ) as $item ) {
        fsi_term_import( $item['name'], 'my_taxonomy' );
    }

Simple as that for **posts, terms and attachments**.
Blazing fast thanks to yields which WordPress is not capable of.


## Features

- Posts
  - Import Posts with `fsi_import_post`.
  - `fsi_post_add_term` creates terms or just adds them if they exist already.
- Media
  - `fsi_import_thumbnail` imports an image from filesystem or URL.
- Term
  - `fsi_term_import` creates a term or just updates if it already exists.
  - `fsi_term_meta_update` eats an array and applies it as new term meta.
  - `fsi_term_meta_replace` like above but with deletion of all old data.

And some neat helpers:

- Use `fsi_query` if you want faster imports.
- Do `fsi_enable_all_caps()` to supercharge your import
  and give it all kind of capabilities.
- Or `fsi_enable_caps` for a subset of caps.

## Real life examples

### Import tt_news from Typo

Here I had some typo instance and had to import all news as new posts.
A simple query and forwarding to a function.
That's all:

    $query = '
    SELECT
      uid									AS _import_uid,
      title                                 AS post_title,
      "post"                                AS post_type,
      short									AS post_excerpt,
      bodytext							    AS post_content,
      CONCAT('http://example.org/', logo)   AS _thumbnail_id,
    FROM `typo3-databse`.tt_news
    ';
    
    foreach( fsi_query ( $query ) as $item ) {
        fsi_import_post( $item );
    }

This does a lot:

- **No duplicates** - Run this several times without hurt.
- **Faster import** - Using `fsi_query` will yield data through.
- **Downloads thumbnail only once** - No duplicates there too.
