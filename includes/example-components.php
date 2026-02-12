<?php
/**
 * Example Components
 * Starter components demonstrating WPWay features
 */

namespace WPWay\Components;

use WPWay\Core\Component;
use WPWay\Core\VirtualDOM as VDOM;

if (!defined('ABSPATH')) exit;

/**
 * Blog List Component
 */
class BlogList extends Component {
    public function render() {
        $this->setState('posts_per_page', $this->props['posts_per_page'] ?? 5);
        $this->setState('columns', $this->props['columns'] ?? 3);

        $args = [
            'posts_per_page' => $this->getState('posts_per_page'),
            'post_type' => 'post',
            'post_status' => 'publish'
        ];

        $posts = get_posts($args);

        $html = sprintf(
            '<div class="wpway-blog-list wpway-grid-columns-%d">',
            (int)$this->getState('columns')
        );

        foreach ($posts as $post) {
            $html .= $this->renderPostCard($post);
        }

        $html .= '</div>';

        return $html;
    }

    private function renderPostCard($post) {
        return sprintf(
            '<article class="wpway-post-card">
                <h3><a href="%s">%s</a></h3>
                %s
                <a href="%s" class="wpway-read-more">Read More →</a>
            </article>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            $this->props['show_excerpt'] ?? true ? '<p>' . esc_html(wp_trim_words(get_the_excerpt($post->ID), 15)) . '</p>' : '',
            esc_url(get_permalink($post->ID))
        );
    }
}

/**
 * Post Card Component
 */
class PostCard extends Component {
    public function render() {
        $post_id = $this->props['post_id'] ?? null;
        if (!$post_id) {
            return '';
        }

        $post = get_post($post_id);
        if (!$post) {
            return '';
        }

        return sprintf(
            '<article class="wpway-post-card">
                <div class="wpway-post-thumbnail">
                    %s
                </div>
                <div class="wpway-post-content">
                    <h3><a href="%s">%s</a></h3>
                    <div class="wpway-post-meta">
                        <span class="wpway-author">%s</span>
                        <span class="wpway-date">%s</span>
                    </div>
                    <p>%s</p>
                    <a href="%s" class="wpway-button">Read More</a>
                </div>
            </article>',
            has_post_thumbnail($post->ID) ? get_the_post_thumbnail($post->ID, 'medium') : '',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html(get_the_author_meta('display_name', $post->post_author)),
            esc_html(get_the_date('F j, Y', $post->ID)),
            esc_html(wp_trim_words(get_the_excerpt($post->ID), 20)),
            esc_url(get_permalink($post->ID))
        );
    }
}

/**
 * Hero Component
 */
class Hero extends Component {
    public function render() {
        $title = $this->props['attributes']['title'] ?? 'Welcome';
        $subtitle = $this->props['attributes']['subtitle'] ?? '';
        $bg_color = $this->props['attributes']['background_color'] ?? '#ffffff';
        $button_text = $this->props['attributes']['button_text'] ?? 'Learn More';
        $button_url = $this->props['attributes']['button_url'] ?? '#';

        $style = sprintf(
            'background-color: %s;',
            esc_attr($bg_color)
        );

        return sprintf(
            '<section class="wpway-hero" style="%s">
                <div class="wpway-hero-content">
                    <h1>%s</h1>
                    %s
                    <a href="%s" class="wpway-button wpway-button-primary">%s</a>
                </div>
            </section>',
            $style,
            esc_html($title),
            $subtitle ? '<p class="wpway-hero-subtitle">' . esc_html($subtitle) . '</p>' : '',
            esc_url($button_url),
            esc_html($button_text)
        );
    }
}

/**
 * Newsletter Component
 */
class Newsletter extends Component {
    public function render() {
        $heading = $this->props['attributes']['heading'] ?? 'Subscribe to our newsletter';
        $placeholder = $this->props['attributes']['placeholder'] ?? 'Enter your email';
        $button_text = $this->props['attributes']['button_text'] ?? 'Subscribe';

        return sprintf(
            '<section class="wpway-newsletter">
                <div class="wpway-newsletter-content">
                    <h2>%s</h2>
                    <form class="wpway-newsletter-form" data-wpway-form="newsletter">
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="%s" 
                            required 
                            class="wpway-input"
                        >
                        <button type="submit" class="wpway-button wpway-button-primary">%s</button>
                    </form>
                </div>
            </section>',
            esc_html($heading),
            esc_attr($placeholder),
            esc_html($button_text)
        );
    }
}

/**
 * Recent Posts Component
 */
class RecentPosts extends Component {
    public function render() {
        $count = intval($this->props['count'] ?? 3);
        
        $args = [
            'posts_per_page' => $count,
            'post_type' => 'post',
            'post_status' => 'publish'
        ];

        $posts = get_posts($args);

        if (empty($posts)) {
            return '<div class="wpway-recent-posts-empty">No posts found</div>';
        }

        $html = '<div class="wpway-recent-posts">';

        foreach ($posts as $post) {
            $html .= sprintf(
                '<div class="wpway-recent-post">
                    <a href="%s">%s</a>
                    <time>%s</time>
                </div>',
                esc_url(get_permalink($post->ID)),
                esc_html(get_the_title($post->ID)),
                esc_html(get_the_date('F j, Y', $post->ID))
            );
        }

        $html .= '</div>';

        return $html;
    }
}

/**
 * Archive Component
 */
class Archive extends Component {
    public function render() {
        $post_type = $this->props['post_type'] ?? 'post';
        $posts_per_page = intval($this->props['posts_per_page'] ?? 10);
        $page = intval(get_query_var('paged')) ?: 1;

        $args = [
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'post_status' => 'publish'
        ];

        $query = new \WP_Query($args);

        $html = '<div class="wpway-archive">';

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $html .= sprintf(
                    '<article class="wpway-archive-item">
                        <h3><a href="%s">%s</a></h3>
                        <p>%s</p>
                    </article>',
                    esc_url(get_permalink()),
                    esc_html(get_the_title()),
                    esc_html(wp_trim_words(get_the_excerpt(), 20))
                );
            }

            wp_reset_postdata();
        } else {
            $html .= '<p>No posts found</p>';
        }

        $html .= '</div>';

        return $html;
    }
}
