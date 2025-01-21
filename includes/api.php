<?php
if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function () {
    // Register the /genres endpoint
    register_rest_route('bestsellers/v1', '/genres', [
        'methods' => 'GET',
        'callback' => function () {
            $api_key = '7fqge2qgxcdrwqbcgeywwdj2';
            $url = 'https://api.penguinrandomhouse.com/resources/v2/title/domains/PRH.UK/categories?rows=15&catSetId=PW&api_key='.$api_key;

            $response = wp_remote_get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key
                ],
            ]);
            //print_r($response);
            // Handle request errors
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                error_log("Error fetching genres: $error_message");
                return new WP_Error('api_error', 'Unable to fetch genres', ['status' => 500]);
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);
            //print_r($data);

            // Log the response body for debugging purposes
            error_log('API Response: ' . print_r($data, true));

            // Check if 'data' and 'categories' exist and return them
            if (isset($data['data']['categories']) && is_array($data['data']['categories'])) {
                $genres = array_map(function($category) {
                    return [
                        'description' => $category['description'] ?? 'Unknown Category',
                        'catUri' => $category['catUri'] ?? ''
                    ];
                }, $data['data']['categories']);

                return ['categories' => $genres];
            }

            return new WP_Error('api_error', 'Invalid API response format', ['status' => 500]);
        },
        'permission_callback' => '__return_true', // Allows public access
    ]);
});

// Register the /books endpoint
function bestsellers_register_routes() {
    register_rest_route('bestsellers/v1', '/books', [
        'methods' => 'GET',
        'callback' => 'bestsellers_get_books_by_genre',
        'permission_callback' => '__return_true', // Allows public access
        'args' => [
            'catUri' => [
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param); // Ensure catUri is not empty
                }
            ],
        ],
    ]);
}

add_action('rest_api_init', 'bestsellers_register_routes');

// Callback function to fetch books from the Biblio API
function bestsellers_get_books_by_genre($data) {
    $catUri = sanitize_text_field($data['catUri']);
    $api_key = '7fqge2qgxcdrwqbcgeywwdj2';
    $url = "https://api.penguinrandomhouse.com/resources/v2/title/domains/PRH.UK/works/views/uk-list-display?rows=15&catUri={$catUri}&catSetId=PW&sort=weeklySales&dir=desc&api_key={$api_key}";

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key
        ],
    ]);

    if (is_wp_error($response)) {
        return new WP_Error('bestsellers_api_error', 'Unable to fetch books.', ['status' => 500]);
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['data']['works']) && is_array($data['data']['works'])) {
        return $data['data']['works'];
    }

    return new WP_Error('bestsellers_no_books', 'No books found for the selected category.', ['status' => 404]);
}
