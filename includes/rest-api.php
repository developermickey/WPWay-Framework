<?php

add_action('rest_api_init', function () {

    register_rest_route('wpway/v1', '/page', [
        'methods'  => 'GET',
        'callback' => 'wpway_get_page',
        'permission_callback' => '__return_true'
    ]);

});

function wpway_get_page($request) {

    $url = esc_url_raw($request->get_param('url'));

    if (!$url) {
        return new WP_REST_Response(['error' => 'No URL provided'], 400);
    }

    $post_id = url_to_postid($url);

    if (!$post_id) {
        return new WP_REST_Response(['error' => 'Invalid URL'], 404);
    }

    $post = get_post($post_id);

    return [
        'title'   => get_the_title($post_id),
        'content' => apply_filters('the_content', $post->post_content),
    ];
}
