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

### Mapping

There is a helper for mapping included. Basically it is an array with some magic:

- The **key** is where the data should go.
- The **value** is where the data comes from.
- So to speak data flows from the right side of `[ 'target_field' => 'source_field' ]` to the left

So it is like:

```php
$mapping = new WP_FSI\Mapping();

$mapping['target_column_name'] = 'source_column_name';
$mapping['_import_uid']        = 'uid';
$mapping['post_title']         = 'subject';
$mapping['post_excerpt']       = 'This is no field of the source, so the string / value will be stored.';
$mapping['i_am_meta']          = 42; // Also no field of the source? Then all those meta_fields " will have the value 42.

// And for now it is very easy piping all through.
$some_data_source = fetched_from_somewhere();
$post_data = $mapping( $some_data_source );
fsi_import_post( $post_data );
```

**Callables** on the value side help you transform data.
What you return is what will be stored in the target:

```php
$mapping = new WP_FSI\Mapping();

$mapping['post_excerpt'] = function( $mapping_object, $source_data, &$target_data ) {
   
    // The FIRST ARGUMENT is the mapping itself so that you can delegate.
    if ( is_callable( $mapping_object['some_callable'] ) ) {
        return call_user_func_array( $mapping_object['some_callable'], func_get_args() );
    }
    
    // The SECOND ARGUMENT is where everything came from.
    if ( 'dog' == $source_data['animal'] ) {
        return 'Such fast. Very simple. Much wow!';
    }
    
    // The THIRD ARGUMENT is the target data that you still can manipulate.
    if ( 'cat' == $source_data['animal'] ) {
        shuffle( $target_data );
        $target_data['post_title'] = 'meow meow!';
    }
    
    // or imagine sub queries here, data manipulation and more
    return 'Last seen on ' . date( 'Y-m-d', $source_data['timestamp'] );
}

// Still the same and easy.
$some_data_source = fetched_from_somewhere();
$post_data = $mapping( $some_data_source );
fsi_import_post( $post_data );
```

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

You can also use the mapper for that:
