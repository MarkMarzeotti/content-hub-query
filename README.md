# Content Hub Query

A WordPress plugin that facilitates easier use of WP_Query and easy logic switching with some dedicated methods.

## Features

`CH_Query` automatically keeps track of posts that have been displayed on the page already. This makes it easy to display lists of posts by any taxonomy and ensure you do not end up displaying duplicates.

Easily apply logic to the query after it has been created for a better flow of logic.

## How to use

You would set up your query similarly to how you would using WP_Query. The one addition would be using the run method. So far more complicated, I know.

```php
$query = new CH_Query();

$posts = $query->run();
```

You can pass any arguments that you would normally pass `WP_Query`.

```php
$args = array(
    'posts_per_page' => 5
);

$query = new CH_Query( $args );
```

There are methods set up to quickly and easily add more complex parameters with a single line of code.

### Query in a certain taxonomy (defaults to Category)

Query for posts in "animals" category.

```php
$query->in_tax( 'animals' );
```

The `in_tax` method will accept a slug, term_id, and arrays of either slugs or term_id.

#### Example use

Say you want to allow users to optionally sort posts by a number of categories. Your `WP_Query` might looks comething like this:

```php
$args = array(
    'post_type'      => array( 'post' ),
    'post_status'    => array( 'publish' ),
    'posts_per_page' => 5
);

if ( $user_wants === 'to sort by category' ) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => 'animals',
        )
    )
}

$query = new WP_Query( $args );
```

Using `CH_Query` simplifies this a bit by allowing you to apply the tax_query logic using its method.

```php
$args = array(
    'posts_per_page' => 5
);

$query = new CH_Query( $args );

if ( $user_wants === 'to sort by category' ) {
    $query->in_tax( 'animals' );
}

$posts = $query->run();
```

To use the `in_tax` method with a custom taxonomy, pass the taxonomy name as the second parameter.

If you were looking for posts labeled 'zebra' in the 'animals' taxonomy:

```php
$query->in_tax( 'zebra', 'animals' );
```