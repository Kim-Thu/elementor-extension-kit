<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\TaxonomyList;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use WP_Error;
use WP_Taxonomy;

final class TaxonomyList extends Widget_Base
{
    public function get_name(): string { return 'eek-taxonomy-list'; }
    public function get_title(): string { return esc_html__('EEK Taxonomy List', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-taxonomy-list']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('query', ['label' => esc_html__('Terms', 'elementor-extension-kit')]);
        $this->add_control('taxonomy', [
            'label' => esc_html__('Taxonomy', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'category',
            'options' => $this->taxonomyOptions(),
        ]);
        $this->add_control('hide_empty', [
            'label' => esc_html__('Hide empty', 'elementor-extension-kit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);
        $this->add_control('orderby', [
            'label' => esc_html__('Order by', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'name',
            'options' => [
                'name' => esc_html__('Name', 'elementor-extension-kit'),
                'count' => esc_html__('Count', 'elementor-extension-kit'),
                'term_id' => esc_html__('Term ID', 'elementor-extension-kit'),
                'slug' => esc_html__('Slug', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('order', [
            'label' => esc_html__('Order', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'ASC',
            'options' => ['ASC' => 'ASC', 'DESC' => 'DESC'],
        ]);
        $this->add_control('show_count', [
            'label' => esc_html__('Show count', 'elementor-extension-kit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        $this->add_control('show_description', [
            'label' => esc_html__('Show description', 'elementor-extension-kit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);
        $this->add_control('hierarchy', [
            'label' => esc_html__('Hierarchy', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'all',
            'options' => [
                'all' => esc_html__('All terms', 'elementor-extension-kit'),
                'top' => esc_html__('Top level only', 'elementor-extension-kit'),
            ],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? 'category'));
        if (! taxonomy_exists($taxonomy)) {
            return;
        }

        $allowedOrderby = ['name', 'count', 'term_id', 'slug'];
        $orderby = in_array(($settings['orderby'] ?? ''), $allowedOrderby, true) ? (string) $settings['orderby'] : 'name';
        $args = [
            'taxonomy' => $taxonomy,
            'hide_empty' => ($settings['hide_empty'] ?? '') === 'yes',
            'orderby' => $orderby,
            'order' => ($settings['order'] ?? '') === 'DESC' ? 'DESC' : 'ASC',
        ];
        if (($settings['hierarchy'] ?? '') === 'top') {
            $args['parent'] = 0;
        }

        $terms = get_terms($args);
        if ($terms instanceof WP_Error || ! is_array($terms) || $terms === []) {
            return;
        }
        ?>
        <ul class="eek-taxonomy-list" aria-label="<?php echo esc_attr__('Taxonomy terms', 'elementor-extension-kit'); ?>">
            <?php foreach ($terms as $term) : ?>
                <?php
                $link = get_term_link($term);
                if ($link instanceof WP_Error) { continue; }
                ?>
                <li class="eek-taxonomy-list__item">
                    <a class="eek-taxonomy-list__link" href="<?php echo esc_url($link); ?>">
                        <span class="eek-taxonomy-list__name"><?php echo esc_html($term->name); ?></span>
                        <?php if (($settings['show_count'] ?? '') === 'yes') : ?><span class="eek-taxonomy-list__count" aria-label="<?php echo esc_attr(sprintf(esc_html__('%d items', 'elementor-extension-kit'), (int) $term->count)); ?>"><?php echo esc_html((string) (int) $term->count); ?></span><?php endif; ?>
                    </a>
                    <?php if (($settings['show_description'] ?? '') === 'yes' && trim((string) $term->description) !== '') : ?><p class="eek-taxonomy-list__description"><?php echo esc_html((string) $term->description); ?></p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }

    /** @return array<string,string> */
    private function taxonomyOptions(): array
    {
        $options = [];
        foreach (get_taxonomies(['public' => true], 'objects') as $name => $object) {
            if ($object instanceof WP_Taxonomy) {
                $options[(string) $name] = (string) $object->labels->singular_name;
            }
        }
        return $options !== [] ? $options : ['category' => esc_html__('Category', 'elementor-extension-kit')];
    }
}
