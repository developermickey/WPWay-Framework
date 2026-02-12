<?php

add_action('rest_api_init', function () {

    register_rest_route('wpway/v1', '/component', [
        'methods'  => 'GET',
        'callback' => 'wpway_get_component',
        'permission_callback' => '__return_true'
    ]);

});

function wpway_get_component($request) {

    $name = sanitize_text_field($request->get_param('name'));
    $id   = intval($request->get_param('id'));

    if ($name === 'post-card') {

        $post = get_post($id);

        return [
            'title' => get_the_title($id),
            'excerpt' => get_the_excerpt($id),
            'link' => get_permalink($id)
        ];
    }

    return ['error' => 'Component not found'];
}
