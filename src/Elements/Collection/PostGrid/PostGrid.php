<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\PostGrid;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Shared\PaginationLinks;
use WP_Post_Type;
use WP_Taxonomy;
use WP_Query;

final class PostGrid extends Widget_Base
{
    public function get_name(): string { return 'eek-post-grid'; }
    public function get_title(): string { return esc_html__('EEK Post Grid', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-post-grid', 'eek-pagination']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('query', ['label' => esc_html__('Query', 'elementor-extension-kit')]);
        $this->add_control('post_type', ['label' => esc_html__('Post type', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'post', 'options' => $this->postTypeOptions()]);
        $this->add_control('posts_per_page', ['label' => esc_html__('Posts per page', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 50, 'default' => 6]);
        $this->add_control('orderby', ['label' => esc_html__('Order by', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'date', 'options' => ['date' => esc_html__('Date', 'elementor-extension-kit'), 'title' => esc_html__('Title', 'elementor-extension-kit'), 'modified' => esc_html__('Modified', 'elementor-extension-kit'), 'menu_order' => esc_html__('Menu order', 'elementor-extension-kit'), 'rand' => esc_html__('Random', 'elementor-extension-kit')]]);
        $this->add_control('order', ['label' => esc_html__('Order', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'DESC', 'options' => ['DESC' => 'DESC', 'ASC' => 'ASC']]);
        $this->add_control('taxonomy', ['label' => esc_html__('Taxonomy filter', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => $this->taxonomyOptions()]);
        $this->add_control('terms', ['label' => esc_html__('Term slugs', 'elementor-extension-kit'), 'description' => esc_html__('Comma-separated slugs.', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT]);
        $this->end_controls_section();

        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('show_image', ['label' => esc_html__('Featured image', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('show_date', ['label' => esc_html__('Date', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('show_excerpt', ['label' => esc_html__('Excerpt', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('empty_message', ['label' => esc_html__('Empty message', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('No posts found.', 'elementor-extension-kit')]);
        $this->end_controls_section();

        $this->start_controls_section('layout', ['label' => esc_html__('Layout', 'elementor-extension-kit'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('columns', ['label' => esc_html__('Columns', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 5, 'default' => 3, 'tablet_default' => 2, 'mobile_default' => 1, 'selectors' => ['{{WRAPPER}} .eek-post-grid' => '--eek-post-grid-columns: {{VALUE}};']]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $postType = sanitize_key((string) ($settings['post_type'] ?? 'post'));
        if (! post_type_exists($postType)) { $postType = 'post'; }

        $allowedOrderby = ['date', 'title', 'modified', 'menu_order', 'rand'];
        $orderby = in_array(($settings['orderby'] ?? ''), $allowedOrderby, true) ? (string) $settings['orderby'] : 'date';
        $order = ($settings['order'] ?? '') === 'ASC' ? 'ASC' : 'DESC';
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
        $args = [
            'post_type' => $postType,
            'post_status' => 'publish',
            'posts_per_page' => max(1, min(50, (int) ($settings['posts_per_page'] ?? 6))),
            'orderby' => $orderby,
            'order' => $order,
            'paged' => $paged,
            'ignore_sticky_posts' => true,
        ];

        $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? ''));
        $rawTerms = preg_split('/\s*,\s*/', (string) ($settings['terms'] ?? '')) ?: [];
        $terms = array_values(array_filter(array_map('sanitize_title', $rawTerms), static fn(string $term): bool => $term !== ''));
        if ($taxonomy !== '' && taxonomy_exists($taxonomy) && $terms !== []) {
            $args['tax_query'] = [[
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $terms,
            ]];
        }

        $query = new WP_Query($args);
        if (! $query->have_posts()) {
            $emptyMessage = trim((string) ($settings['empty_message'] ?? ''));
            if ($emptyMessage !== '') { ?><p class="eek-post-grid__empty"><?php echo esc_html($emptyMessage); ?></p><?php }
            return;
        }
        ?>
        <div class="eek-post-grid" role="list">
            <?php foreach ($query->posts as $post) : ?>
                <?php
                $postId = (int) $post->ID;
                $permalink = get_permalink($postId);
                $title = get_the_title($postId);
                $imageUrl = get_the_post_thumbnail_url($postId, 'medium_large') ?: '';
                $excerpt = trim((string) get_post_field('post_excerpt', $postId));
                if ($excerpt === '') { $excerpt = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $postId)), 24); }
                ?>
                <article class="eek-post-grid__card" role="listitem">
                    <?php if (($settings['show_image'] ?? '') === 'yes' && $imageUrl !== '') : ?><a class="eek-post-grid__media" href="<?php echo esc_url($permalink); ?>"><img src="<?php echo esc_url($imageUrl); ?>" alt="" loading="lazy" decoding="async"></a><?php endif; ?>
                    <div class="eek-post-grid__body">
                        <?php if (($settings['show_date'] ?? '') === 'yes') : ?><time class="eek-post-grid__date" datetime="<?php echo esc_attr(get_the_date(DATE_W3C, $postId)); ?>"><?php echo esc_html(get_the_date('', $postId)); ?></time><?php endif; ?>
                        <h3 class="eek-post-grid__title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h3>
                        <?php if (($settings['show_excerpt'] ?? '') === 'yes' && $excerpt !== '') : ?><p class="eek-post-grid__excerpt"><?php echo esc_html($excerpt); ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        $links = PaginationLinks::build($paged, (int) $query->max_num_pages, esc_html__('Previous', 'elementor-extension-kit'), esc_html__('Next', 'elementor-extension-kit'));
        if ($links !== []) : ?>
            <nav class="eek-pagination eek-post-grid__pagination" aria-label="<?php echo esc_attr__('Post pagination', 'elementor-extension-kit'); ?>">
                <ul class="eek-pagination__list"><?php foreach ($links as $link) : ?><li class="eek-pagination__item"><?php echo wp_kses_post($link); ?></li><?php endforeach; ?></ul>
            </nav>
        <?php endif;
    }

    /** @return array<string,string> */
    private function postTypeOptions(): array
    {
        $options = [];
        foreach (get_post_types(['public' => true], 'objects') as $name => $object) {
            if ($object instanceof WP_Post_Type) { $options[(string) $name] = (string) $object->labels->singular_name; }
        }
        return $options !== [] ? $options : ['post' => esc_html__('Post', 'elementor-extension-kit')];
    }

    /** @return array<string,string> */
    private function taxonomyOptions(): array
    {
        $options = ['' => esc_html__('None', 'elementor-extension-kit')];
        foreach (get_taxonomies(['public' => true], 'objects') as $name => $object) {
            if ($object instanceof WP_Taxonomy) { $options[(string) $name] = (string) $object->labels->singular_name; }
        }
        return $options;
    }
}
