# Gutenberg React WP Bestseller Book Biblio Block

This repository contains a custom WordPress Gutenberg block developed using React to display bestseller book lists dynamically. The block fetches and displays book information, such as title, author, image, and a "Buy Now" link, based on the selected genre. It is designed to be easily integrated into any WordPress website that supports the Gutenberg block editor.

## Features

- Display a dynamic list of bestselling books based on genre selection.
- Fetch book details including title, author, cover image, and a "Buy Now" link from a REST API endpoint.
- Easy-to-use Gutenberg block for integration with WordPress.
- Fully responsive design to cater to various screen sizes.
- Customizable block attributes for genre and title.

## Installation

1. Clone or download this repository.
2. Upload the block to your WordPress site.
3. Activate the plugin from the WordPress admin dashboard.

Alternatively, you can add the plugin using the following steps:

1. Navigate to `wp-content/plugins` and create a new directory for the plugin.
2. Upload the contents of this repository into the newly created directory.
3. Go to the WordPress admin dashboard and activate the plugin.

## Usage

1. Once the plugin is activated, go to any post or page in the WordPress editor.
2. Search for the "Bestseller Book Biblio Block" in the block inserter.
3. Add the block to your content.
4. Set the genre and title in the block settings on the right sidebar.
5. Publish the post or page, and the books will be dynamically loaded based on the selected genre.

## Block Attributes

- **Genre**: Select a genre for the bestseller list.
- **Title**: Set a custom title for the block (e.g., "Bestsellers - Fiction").

## API Endpoints

### `bestsellers/v1/genres`

- **Method**: GET
- **Description**: Fetches available genres for the bestseller list.
- **Response**: Returns a list of genres.

### `bestsellers/v1/books`

- **Method**: GET
- **Parameters**:
  - `genre` (required): The genre for fetching books (e.g., "fiction", "mystery", etc.).
  
- **Example**: `/wp-json/bestsellers/v1/books?genre=fiction`
- **Response**: Returns a list of books in the specified genre. Each book object contains:
  - `title`: The title of the book.
  - `author`: The author of the book.
  - `image`: URL to the cover image.
  - `buy_link`: Link to purchase the book.

## Example Usage

```javascript
// Example of using the Gutenberg block in your template or post
<BestsellerBookBiblioBlock genre="fiction" title="Bestsellers - Fiction" />
```

## Styling

The block uses custom styles for the following elements:

- **Top header**: Font: Roboto, Size: 25px, Weight: Bold
- **Book title**: Font: Roboto, Size: 24px, Weight: Demi-Bold
- **Author name**: Font: Roboto, Size: 16px, Weight: Regular
- **Buy Now button**: Background color: #FF6401, Font: Roboto, Size: 16px, Weight: Semibold

## Contributing

We welcome contributions to improve the functionality, usability, and performance of the block. To contribute, please follow these steps:

1. Fork this repository.
2. Create a new branch (`git checkout -b feature-name`).
3. Make your changes and commit (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature-name`).
5. Create a new pull request.

## License

This plugin is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Support

For support or questions, open an issue on the GitHub repository or reach out to the repository owner.

```

This update includes the `bestsellers/v1/books` API endpoint, which returns the list of books based on the selected genre, and it includes the necessary fields such as `title`, `author`, `image`, and `buy_link`. This will allow users to fetch book data dynamically and display it via the Gutenberg block.
