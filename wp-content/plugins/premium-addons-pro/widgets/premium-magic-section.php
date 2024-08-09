<?php
/**
 * Class: Premium_Magic_Section
 * Name: Off-Canvas
 * Slug: premium-addon-magic-section
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Responsive\Responsive;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Magic_Section
 */
class Premium_Magic_Section extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-magic-section' );
		return $is_enabled;
	}

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Widget rtl check.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function check_rtl() {
		return is_rtl();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-magic-section';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Off Canvas', 'premium-addons-pro' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-magic-section';
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'elementor-icons',
			'premium-addons',
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		$draw_scripts = $this->check_icon_draw() ? array(
			'pa-fontawesome-all',
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'premium-pro',
				'lottie-js',
				'pa-svgsnap',
			)
		);
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'magic', 'slide', 'section' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Magic Section controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_content_type',
			array(
				'label'       => __( 'Content to Show', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'editor'   => __( 'Text Editor', 'premium-addons-pro' ),
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'     => 'editor',
				'label_block' => true,
			)
		);

		$this->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'premium_magic_section_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_content_temp_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'premium_magic_section_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'premium_magic_section_content_type' => 'template',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_magic_section_content',
			array(
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => array( 'active' => true ),
				'default'    => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et hendrerit lacus. Donec eu neque leo. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras luctus, lectus viverra tincidunt dictum, nibh lorem rhoncus tellus, quis ullamcorper orci velit at nisl. Nunc sed tempor ligula. Morbi a tellus orci. Etiam pharetra vitae diam vitae faucibus. Pellentesque bibendum, odio sed gravida sagittis, dui dui porta ante, a finibus nisl arcu eget augue. Quisque nec dapibus ex, at consequat nunc. Phasellus elementum tellus id lacus tempus, id aliquam erat faucibus. Cras fringilla massa eu lorem interdum lacinia. Sed lobortis congue purus, vitae commodo turpis dignissim sed. Ut eget felis sed ante pellentesque convallis quis at quam. Fusce molestie lacus felis, sed finibus lorem efficitur id. Suspendisse sagittis ipsum orci, sit amet pretium ligula fermentum id.',
				'condition'  => array(
					'premium_magic_section_content_type' => 'editor',
				),
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => __( 'Text Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'#premium-magic-section-{{ID}} .premium-msection-content-wrap' => 'text-align: {{VALUE}}',
				),
				'default'   => 'center',
				'condition' => array(
					'premium_magic_section_content_type' => 'editor',
				),
			)
		);

		$this->add_responsive_control(
			'content_position',
			array(
				'label'     => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'selectors' => array(
					'#premium-magic-section-{{ID}}' => 'align-items: {{VALUE}}',
				),
				'default'   => 'center',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'trigger_section',
			array(
				'label' => __( 'Trigger', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'        => __( 'Trigger', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'button',
				'options'      => array(
					'button'   => __( 'Button', 'premium-addons-pro' ),
					'icon'     => __( 'Icon', 'premium-addons-pro' ),
					'lottie'   => __( 'Lottie Animation', 'premium-addons-pro' ),
					'image'    => __( 'Image', 'premium-addons-pro' ),
                    'svg'      => __( 'SVG Code', 'premium-addons-pro' ),
					'selector' => __( 'CSS Selector', 'premium-addons-pro' ),
				),
				'prefix_class' => 'offcanvas-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'css_selector',
			array(
				'label'       => __( 'CSS Selector', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Add the CSS selector of the element that will trigger the off canvas. For example, #element-id or .element-class', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'trigger' => 'selector',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_button_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'default'     => __( 'Premium Off-Canvas Section', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'trigger' => 'button',
				),
			)
		);

		$common_conditions = array(
			'trigger'                             => 'button',
			'premium_magic_section_icon_switcher' => 'yes',
		);

		$this->add_control(
			'premium_magic_section_icon_switcher',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'trigger' => 'button',
				),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon'   => __( 'Icon', 'premium-addons-pro' ),
					'image'  => __( 'Image', 'premium-addons-pro' ),
					'lottie' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'    => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'default'   => 'icon',
				'condition' => $common_conditions,
			)
		);

		$this->add_control(
			'new_button_icon_selection',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'default'          => array(
					'value'   => 'fa fa-star',
					'library' => 'solid',
				),
				'conditions'    => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'trigger',
                            'value' => 'icon'
                        ],
                        [
                            'terms' => [
                                array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'icon',
								),
                            ]
                        ]

                    ]
                ]
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'conditions'    => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'trigger',
                            'value' => 'svg'
                        ],
                        [
                            'terms' => [
                                array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'svg',
								),
                            ]
                        ]

                    ]
                ]
			)
		);

        $draw_icon_conditions = [
            'terms' => [
                [
                    'name' => 'new_button_icon_selection[library]',
                    'operator' => '!==',
                    'value' => 'svg'
                ],
                [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'trigger',
                            'value' => 'icon'
                        ],
                        [
                            'name' => 'trigger',
                            'value' => 'svg'
                        ],
                        [
                            'terms' => [
                                array(
                                    'name'  => 'trigger',
                                    'value' => 'button',
                                ),
                                array(
                                    'name'  => 'premium_magic_section_icon_switcher',
                                    'value' => 'yes',
                                ),
                                [
                                    'relation' => 'or',
                                    'terms' => [
                                        [
                                            'name'  => 'icon_type',
                                            'value' => 'icon',
                                        ],
                                        [
                                            'name'  => 'icon_type',
                                            'value' => 'svg',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],

        ];

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'conditions' => $draw_icon_conditions
			)
		);

        $draw_icon_conditions['terms'] = array_merge( $draw_icon_conditions['terms'], [array(
            'name' => 'draw_svg',
            'value'=> 'yes'
        )]);

        if ( $draw_icon ) {

            $this->add_control(
                'path_width',
                array(
                    'label'     => __( 'Path Thickness', 'premium-addons-pro' ),
                    'type'      => Controls_Manager::SLIDER,
                    'range'     => array(
                        'px' => array(
                            'min'  => 0,
                            'max'  => 50,
                            'step' => 0.1,
                        ),
                    ),
                    'conditions'    => $draw_icon_conditions,
                    'selectors' => array(
                        '{{WRAPPER}} .premium-msection-btn svg:not(.premium-btn-svg) *' => 'stroke-width: {{SIZE}}',
                    ),
                )
            );

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'conditions'    => $draw_icon_conditions
				)
			);

			$this->add_control(
				'svg_loop',
				array(
					'label'        => __( 'Loop', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'default'      => 'true',
					'conditions'    => $draw_icon_conditions,
				)
			);

			$this->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'conditions'    => $draw_icon_conditions,
				)
			);

			$this->add_control(
				'svg_reverse',
				array(
					'label'        => __( 'Reverse', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'conditions'    => $draw_icon_conditions,
				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'conditions'    => $draw_icon_conditions,
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'conditions'    => $draw_icon_conditions,
				)
			);

		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'canvas',
				array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'new_button_icon_selection[library]!' => 'svg',
					)
				)
			);
		}

		$this->add_control(
			'premium_magic_section_custom_image',
			array(
				'label'      => __( 'Select Image', 'premium-addons-pro' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array( 'active' => true ),
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'image',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'image',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_source',
			array(
				'label'      => __( 'Source', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'url'  => __( 'External URL', 'premium-addons-for-elementor' ),
					'file' => __( 'Media File', 'premium-addons-for-elementor' ),
				),
				'default'    => 'url',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'lottie',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'lottie',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'conditions'  => array(
					'terms' => array(
						array(
							'name'  => 'lottie_source',
							'value' => 'url',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'trigger',
									'value' => 'lottie',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'trigger',
											'value' => 'button',
										),
										array(
											'name'  => 'premium_magic_section_icon_switcher',
											'value' => 'yes',
										),
										array(
											'name'  => 'icon_type',
											'value' => 'lottie',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_file',
			array(
				'label'      => __( 'Upload JSON File', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'lottie_source',
							'value' => 'file',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'trigger',
									'value' => 'lottie',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'trigger',
											'value' => 'button',
										),
										array(
											'name'  => 'premium_magic_section_icon_switcher',
											'value' => 'yes',
										),
										array(
											'name'  => 'icon_type',
											'value' => 'lottie',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'lottie',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'lottie',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'lottie',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'lottie',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'lottie_hover',
			array(
				'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'lottie',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'lottie',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_icon_position',
			array(
				'label'     => __( 'Icon Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'2'  => array(
						'title' => __( 'Before', 'premium-addons-pro' ),
						'icon'  => 'eicon-order-start',
					),
					'-1' => array(
						'title' => __( 'After', 'premium-addons-pro' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '2',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-button-text-icon-wrapper' => 'order: {{VALUE}}',
				),
				'condition' => array(
					'trigger'                             => 'button',
					'premium_magic_section_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_icon_before_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-msection-btn i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-msection-btn svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}} .premium-msection-btn img' => 'width: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'image',
						),
                        array(
							'name'  => 'trigger',
							'value' => 'icon',
						),
                        array(
							'name'  => 'trigger',
							'value' => 'svg',
						),
						array(
							'name'  => 'trigger',
							'value' => 'lottie',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-msection-btn' => 'column-gap: {{SIZE}}px;',
				),
				'condition' => array(
					'trigger'                             => 'button',
					'premium_magic_section_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_button_size',
			array(
				'label'       => __( 'Button Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'premium-btn-sm'    => __( 'Small', 'premium-addons-pro' ),
					'premium-btn-md'    => __( 'Medium', 'premium-addons-pro' ),
					'premium-btn-lg'    => __( 'Large', 'premium-addons-pro' ),
					'premium-btn-block' => __( 'Block', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'default'     => 'premium-btn-lg',
				'condition'   => array(
					'trigger' => 'button',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_button_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-msection-button-trig' => 'text-align: {{VALUE}}',
				),
				'default'   => 'center',
				'toggle'    => false,
				'condition' => array(
					'premium_magic_section_trig_float!'  => 'yes',
					'premium_magic_section_button_size!' => 'premium-btn-block',
					'trigger!'                           => 'selector',
				),
			)
		);

		if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
			Helper_Functions::add_btn_hover_controls(
				$this,
				array(
					'trigger' => 'button',
					// 'premium_magic_section_trig_float!' => 'yes',
				)
			);
		}

		$this->add_control(
			'premium_magic_section_trig_float',
			array(
				'label'     => __( 'Float', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'trigger!' => 'selector',
				),
			)
		);

		$this->add_responsive_control(
			'float_hpos',
			array(
				'label'        => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'custom' => array(
						'title' => __( 'Custom', 'premium-addons-pro' ),
						'icon'  => 'eicon-cog',
					),
				),
				'prefix_class' => 'premium-msection-icon-',
				'default'      => 'left',
				'condition'    => array(
					'premium_magic_section_trig_float' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'float_custom_hpos',
			array(
				'label'     => __( 'Horizontal Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-msection-btn' => 'left: {{SIZE}}%',
				),
				'condition' => array(
					'premium_magic_section_trig_float' => 'yes',
					'float_hpos'                       => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'float_vpos',
			array(
				'label'        => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
					'custom' => array(
						'title' => __( 'Custom', 'premium-addons-pro' ),
						'icon'  => 'eicon-cog',
					),
				),
				'prefix_class' => 'premium-msection-icon-',
				'default'      => 'bottom',
				'condition'    => array(
					'premium_magic_section_trig_float' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'float_custom_vpos',
			array(
				'label'     => __( 'Vertical Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-msection-btn' => 'top: {{SIZE}}%',
				),
				'condition' => array(
					'premium_magic_section_trig_float' => 'yes',
					'float_vpos'                       => 'custom',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_magic_section_display',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'label'       => __( 'Off Canvas Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'slide'  => __( 'Slide', 'premium-addons-pro' ),
					'corner' => __( 'Corner', 'premium-addons-pro' ),
				),
				'default'     => 'slide',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_magic_section_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'default'   => 'right',
				'condition' => array(
					'type!' => 'corner',
				),
			)
		);

		$this->add_control(
			'corner_position',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'topleft'     => __( 'Top Left', 'premium-addons-pro' ),
					'topright'    => __( 'Top Right', 'premium-addons-pro' ),
					'bottomleft'  => __( 'Bottom Left', 'premium-addons-pro' ),
					'bottomright' => __( 'Bottom Right', 'premium-addons-pro' ),
				),
				'default'   => 'topleft',
				'condition' => array(
					'type' => 'corner',
				),
			)
		);

		$this->add_control(
			'h_transition',
			array(
				'label'       => __( 'Transition', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'overlay'    => __( 'Overlay', 'premium-addons-pro' ),
					'push'       => __( 'Push', 'premium-addons-pro' ),
					'reveal'     => __( 'Reveal', 'premium-addons-pro' ),
					'slidealong' => __( 'Slide Along', 'premium-addons-pro' ),
					'rotate'     => __( '3D Rotate Out', 'premium-addons-pro' ),
					'fall'       => __( 'Fall Down', 'premium-addons-pro' ),
					'elastic'    => __( 'Elastic', 'premium-addons-pro' ),
					'bubble'     => __( 'Bubble', 'premium-addons-pro' ),
				),
				'default'     => 'overlay',
				'label_block' => true,
				'condition'   => array(
					'type'                      => 'slide',
					'premium_magic_section_pos' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'v_transition',
			array(
				'label'       => __( 'Transition', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'overlay' => __( 'Overlay', 'premium-addons-pro' ),
					'push'    => __( 'Push', 'premium-addons-pro' ),
					'reveal'  => __( 'Reveal', 'premium-addons-pro' ),
					'wave'    => __( 'Wave', 'premium-addons-pro' ),
				),
				'default'     => 'overlay',
				'label_block' => true,
				'condition'   => array(
					'type'                      => 'slide',
					'premium_magic_section_pos' => array( 'top', 'bottom' ),
				),
			)
		);

		$this->add_control(
			'c_transition',
			array(
				'label'       => __( 'Transition', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'slide' => __( 'Slide', 'premium-addons-pro' ),
					'morph' => __( 'Morph', 'premium-addons-pro' ),
				),
				'default'     => 'slide',
				'label_block' => true,
				'condition'   => array(
					'type' => 'corner',
				),
			)
		);

		$this->add_control(
			'reveal_notice',
			array(
				'raw'             => __( 'Please note that push/reveal effects don\'t work when position is set to bottom.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'v_transition'              => array( 'push', 'reveal' ),
					'premium_magic_section_pos' => 'bottom',
				),
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => __( 'Content Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
				),
				'selectors'  => array(
					'#premium-magic-section-{{ID}}' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}'                   => '--pa-msection-width: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'type',
							'value' => 'corner',
						),
						array(
							'terms' => array(
								array(
									'name'     => 'type',
									'operator' => '!==',
									'value'    => 'corner',
								),
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_magic_section_pos',
											'value' => 'left',
										),
										array(
											'name'  => 'premium_magic_section_pos',
											'value' => 'right',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'elastic_shape_width',
			array(
				'label'       => __( 'Shape Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'vw', 'custom' ),
				'description' => __( 'IMPORTANT: this field should not be left empty', 'premium-addons-pro' ),
				'default'     => array(
					'size' => 120,
					'unit' => 'px',
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'   => array(
					'#msection-shape-{{ID}}.left, #msection-shape-{{ID}}.right' => 'width: {{SIZE}}{{UNIT}}',
					'#premium-magic-section-{{ID}}.msection-elastic' => '--pa-eshape-w: {{SIZE}}{{UNIT}}',
				),
				'condition'   => array(
					'type'         => 'slide',
					'h_transition' => 'elastic',
				),
			)
		);

		$this->add_control(
			'elastic_shape_duration',
			array(
				'label'     => __( 'Shape Animation Duration (ms)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 350,
				),
				'separator' => 'after',
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 800,
					),
				),
				'condition' => array(
					'type'         => 'slide',
					'h_transition' => 'elastic',
				),
			)
		);

		$this->add_responsive_control(
			'content_height',
			array(
				'label'      => __( 'Content Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
				),
				'selectors'  => array(
					'#premium-magic-section-{{ID}}' => 'height: {{SIZE}}{{UNIT}}',
					'#premium-magic-section-{{ID}} .premium-msection-content-wrap' => 'max-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}'                   => '--pa-msection-height: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'type',
							'value' => 'corner',
						),
						array(
							'terms' => array(
								array(
									'name'     => 'type',
									'operator' => '!==',
									'value'    => 'corner',
								),
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_magic_section_pos',
											'value' => 'top',
										),
										array(
											'name'  => 'premium_magic_section_pos',
											'value' => 'bottom',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'corner_notice',
			array(
				'raw'             => __( 'You can control spacing from style tab -> Content -> Margin', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'type' => 'corner',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_overlay',
			array(
				'label'   => __( 'Overlay', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'.premium-msection-overlay-{{ID}}' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_magic_section_overlay' => 'yes',
				),
			)
		);

		$this->add_control(
			'change_cursor',
			array(
				'label'     => __( 'Change Cursor on Overlay', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_magic_section_overlay' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'close_section',
			array(
				'label' => __( 'Close Icon', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label'   => __( 'Close Icon', 'premium-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'eicon-close'         => array(
						'title' => __( 'close', 'premium-addons-pro' ),
						'icon'  => 'eicon-close',
					),
					'far fa-times-circle' => array(
						'title' => __( 'far-circle', 'premium-addons-pro' ),
						'icon'  => 'far fa-times-circle',
					),
					'fas fa-times-circle' => array(
						'title' => __( 'fas-circle', 'premium-addons-pro' ),
						'icon'  => 'fas fa-times-circle',
					),
					'eicon-ban'           => array(
						'title' => __( 'none', 'premium-addons-pro' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'eicon-close',
			)
		);

		$this->add_control(
			'premium_magic_section_close_pos',
			array(
				'label'                => __( 'Position', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'  => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-left',
					),
					'right' => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'default'              => 'right',
				'selectors_dictionary' => array(
					'left'  => 'left: 10px',
					'right' => 'right: 10px',
				),
				'selectors'            => array(
					'#premium-magic-section-{{ID}} .premium-msection-close' => '{{VALUE}}',
				),
				'condition'            => array(
					'close_icon!' => 'eicon-ban',
				),
			)
		);

		$this->add_control(
			'close_on_outside',
			array(
				'label'     => esc_html__( 'Close On Click Outside Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_magic_section_button_style',
			array(
				'label'     => __( 'Trigger', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'trigger!' => 'selector',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_magic_section_button_typo',
				'selector'  => '{{WRAPPER}} .premium-msection-btn-text',
				'condition' => array(
					'trigger' => 'button',
				),
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'      => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'       => Controls_Manager::COLOR,
				'global'     => false,
				'separator'  => 'after',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'draw_svg',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'trigger',
									'value' => 'icon',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'trigger',
											'value' => 'button',
										),
										array(
											'name'  => 'premium_magic_section_icon_switcher',
											'value' => 'yes',
										),
										array(
											'name'  => 'icon_type',
											'value' => 'icon',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'premium_magic_section_button_style_tabs' );

		$this->start_controls_tab(
			'premium_magic_section_button_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'trigger' => 'button',
				),
				'selectors' => array(
					'{{WRAPPER}}.offcanvas-button .premium-msection-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label'      => __( 'Icon Color', 'premium-addons-pro' ),
				'type'       => Controls_Manager::COLOR,
				'global'     => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors'  => array(
					'{{WRAPPER}}.offcanvas-button .premium-msection-button-trig i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-msection-btn-icon *' => 'fill: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'icon',
						),
                        array(
							'name'  => 'trigger',
							'value' => 'svg',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'icon',
								),
							),
						),
					),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color',
				array(
					'label'      => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'       => Controls_Manager::COLOR,
					'global'     => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'selectors'  => array(
						'{{WRAPPER}} .premium-msection-btn-icon *' => 'stroke: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'  => 'trigger',
								'value' => 'icon',
							),
                            array(
                                'name'  => 'trigger',
                                'value' => 'svg',
                            ),
							array(
								'terms' => array(
									array(
										'name'  => 'trigger',
										'value' => 'button',
									),
									array(
										'name'  => 'premium_magic_section_icon_switcher',
										'value' => 'yes',
									),
									array(
										'name'  => 'icon_type',
										'value' => 'icon',
									),
								),
							),
						),
					),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'premium_magic_section_button_background',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
				'selector'       => '{{WRAPPER}}.offcanvas-lottie .premium-msection-btn, {{WRAPPER}}.offcanvas-button .premium-msection-btn, {{WRAPPER}} .premium-button-style2-shutinhor:before, {{WRAPPER}} .premium-button-style2-shutinver:before, {{WRAPPER}} .premium-button-style5-radialin:before, {{WRAPPER}} .premium-button-style5-rectin:before',
				'condition'      => array(
					'trigger' => array( 'button', 'lottie' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_magic_section_button_border',
				'selector' => '{{WRAPPER}} .premium-msection-btn',
			)
		);

		$this->add_control(
			'premium_magic_section_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-msection-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_magic_section_button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-msection-btn',
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'trigger!' => array( 'image' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-msection-btn, {{WRAPPER}} .premium-button-line6::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_magic_section_button_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'button_text_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'trigger' => 'button',
				),
				'selectors' => array(
					'{{WRAPPER}}.offcanvas-button .premium-msection-btn:hover, {{WRAPPER}} .premium-button-line6::after' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label'      => __( 'Icon Color', 'premium-addons-pro' ),
				'type'       => Controls_Manager::COLOR,
				'global'     => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-msection-btn:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-msection-btn:hover .premium-msection-btn-icon *' => 'fill: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'trigger',
							'value' => 'icon',
						),
                        array(
                            'name'  => 'trigger',
                            'value' => 'svg',
                        ),
						array(
							'terms' => array(
								array(
									'name'  => 'trigger',
									'value' => 'button',
								),
								array(
									'name'  => 'premium_magic_section_icon_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'icon_type',
									'value' => 'icon',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'underline_color',
			array(
				'label'     => __( 'Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-btn-svg' => 'stroke: {{VALUE}};',
					'{{WRAPPER}} .premium-button-line2::before, {{WRAPPER}} .premium-button-line4::before, {{WRAPPER}} .premium-button-line5::before, {{WRAPPER}} .premium-button-line5::after, {{WRAPPER}} .premium-button-line6::before, {{WRAPPER}} .premium-button-line7::before' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'trigger'                           => 'button',
					'premium_magic_section_trig_float!' => 'yes',
					'premium_button_hover_effect'       => 'style8',
				),
			)
		);

		$this->add_control(
			'first_layer_hover',
			array(
				'label'     => __( 'Layer #1 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:before' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'trigger'                           => 'button',
					'premium_magic_section_trig_float!' => 'yes',
					'premium_button_hover_effect'       => 'style7',
				),
			)
		);

		$this->add_control(
			'second_layer_hover',
			array(
				'label'     => __( 'Layer #2 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:after' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'trigger'                           => 'button',
					'premium_magic_section_trig_float!' => 'yes',
					'premium_button_hover_effect'       => 'style7',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'premium_magic_section_button_hover_background',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_TEXT,
						),
					),
				),
				'condition'      => array(
					'trigger'                      => array( 'button', 'lottie' ),
					'premium_button_hover_effect!' => 'style7',
				),
				'selector'       => '{{WRAPPER}}.offcanvas-lottie .premium-msection-btn:hover, {{WRAPPER}}.offcanvas-button .premium-button-none:hover, {{WRAPPER}} .premium-button-style8:hover, {{WRAPPER}} .premium-button-style1:before, {{WRAPPER}} .premium-button-style2-shutouthor:before, {{WRAPPER}} .premium-button-style2-shutoutver:before, {{WRAPPER}} .premium-button-style2-shutinhor, {{WRAPPER}} .premium-button-style2-shutinver, {{WRAPPER}} .premium-button-style2-dshutinhor:before, {{WRAPPER}} .premium-button-style2-dshutinver:before, {{WRAPPER}} .premium-button-style2-scshutouthor:before, {{WRAPPER}} .premium-button-style2-scshutoutver:before, {{WRAPPER}} .premium-button-style5-radialin, {{WRAPPER}} .premium-button-style5-radialout:before, {{WRAPPER}} .premium-button-style5-rectin, {{WRAPPER}} .premium-button-style5-rectout:before, {{WRAPPER}} .premium-button-style6-bg, {{WRAPPER}} .premium-button-style6:before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_magic_section_button_border_hover',
				'selector' => '{{WRAPPER}} .premium-msection-btn:hover',
			)
		);

		$this->add_control(
			'premium_magic_section_button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-msection-btn:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_magic_section_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-msection-btn:hover',
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'trigger!' => 'image',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-msection-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'close_icon_style',
			array(
				'label'     => __( 'Close Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'close_icon!' => 'eicon-ban',
				),
			)
		);

		$this->add_control(
			'close_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}} .premium-msection-close-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					// '#premium-magic-section-{{ID}} .premium-msection-close'      => '',
				),
			)
		);

		$this->add_control(
			'close_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-magic-section-{{ID}} .premium-msection-close-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-magic-section-{{ID}} .premium-msection-close:hover .premium-msection-close-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_icon_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-magic-section-{{ID}} .premium-msection-close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_icon_hover_backcolor',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-magic-section-{{ID}} .premium-msection-close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'close_icon_shadow',
				'selector' => '#premium-magic-section-{{ID}} .premium-msection-close-icon',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'close_icon_border',
				'selector' => '#premium-magic-section-{{ID}} .premium-msection-close',
			)
		);

		$this->add_control(
			'close_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}} .premium-msection-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}} .premium-msection-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}} .premium-msection-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_style_section',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-magic-section-{{ID}} .premium-msection-content-wrap' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'premium_magic_section_content_type' => 'editor',
				),
			)
		);

		$this->add_control(
			'elastic_shape_color',
			array(
				'label'      => __( 'Shape Color', 'premium-addons-pro' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'#msection-shape-{{ID}} path' => 'fill: {{VALUE}}',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'type',
							'value' => 'slide',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'h_transition',
									'value' => 'elastic',
								),
								array(
									'name'  => 'h_transition',
									'value' => 'bubble',
								),
								array(
									'name'  => 'v_transition',
									'value' => 'wave',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'content_typo',
				'selector'  => '#premium-magic-section-{{ID}} .premium-msection-content-wrap',
				'condition' => array(
					'premium_magic_section_content_type' => 'editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'content_shadow',
				'selector'  => '#premium-magic-section-{{ID}} .premium-msection-content-wrap',
				'separator' => 'after',
				'condition' => array(
					'premium_magic_section_content_type' => 'editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'content_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '#premium-magic-section-{{ID}}',
				'condition' => array(
					'h_transition!' => 'bubble',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_border',
				'selector' => '#premium-magic-section-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(

				'name'     => 'content_box_shadow',
				'selector' => '#premium-magic-section-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'type' => 'corner',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-magic-section-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Magic Section widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$position = 'corner' !== $settings['type'] ? $settings['premium_magic_section_pos'] : $settings['corner_position'];

		$is_float = $settings['premium_magic_section_trig_float'];

		$trigger = $settings['trigger'];

		// $this->add_render_attribute( '_wrapper', 'class', 'offcanvas-' . $trigger );

		if ( 'corner' !== $settings['type'] ) {
			if ( in_array( $position, array( 'right', 'left' ) ) ) {
				$transition = $settings['h_transition'];
			} else {
				$transition = $settings['v_transition'];
			}
		} else {
			$transition = $settings['c_transition'];
		}

		$msection_settings = array(
			'position'     => $position,
			'type'         => $settings['type'],
			'trigger'      => $trigger,
			'style'        => $transition,
			'clickOutside' => 'yes' === $settings['close_on_outside'] ? true : false,
		);

		if ( 'elastic' === $transition ) {
			$msection_settings['e_dur'] = $settings['elastic_shape_duration']['size'];
		}

		$this->add_render_attribute( 'container', 'class', 'premium-magic-section-container' );

		$this->add_render_attribute(
			'content_wrap',
			array(
				'id'            => 'premium-magic-section-' . $id,
				'class'         => array(
					'premium-msection-wrap',
					'premium-addons__v-hidden',
					'offcanvas-' . $transition,
					$position,
				),
				'data-settings' => wp_json_encode( $msection_settings ),
			)
		);

		$this->add_render_attribute(
			'overlay',
			array(
				'class' => array(
					'premium-msection-overlay-' . $id,
					'premium-msection-overlay',
					'premium-addons__v-hidden',
				),
			)
		);

		if ( 'yes' === $settings['change_cursor'] ) {

			$this->add_render_attribute( 'overlay', 'class', 'offcanvas-cursor-close' );

		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content_wrap' ) ); ?>>

					<div class="premium-msection-content-wrap">
						<?php
						if ( 'editor' === $settings['premium_magic_section_content_type'] ) :
							echo $this->parse_text_editor( $settings['premium_magic_section_content'] );
						else :
							$template = empty( $settings['premium_magic_section_content_temp'] ) ? $settings['live_temp_content'] : $settings['premium_magic_section_content_temp'];
							echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;
						?>
					</div>


					<div class="premium-msection-close">
						<?php if ( 'eicon-ban' !== $settings['close_icon'] ) : ?>
							<i class="premium-msection-close-icon <?php echo wp_kses_post( $settings['close_icon'] ); ?>"></i>
						<?php endif; ?>
					</div>

					<?php if ( 'elastic' === $transition ) : ?>

						<div id="msection-shape-<?php echo esc_attr( $id ); ?>" class="msection-shape <?php echo esc_attr( $position ); ?>" data-morph-open="M-1,0h101c0,0,0-1,0,395c0,404,0,405,0,405H-1V0z">

							<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 100 800" preserveAspectRatio="none">
								<path d="M-1,0h101c0,0-97.833,153.603-97.833,396.167C2.167,627.579,100,800,100,800H-1V0z"></path>
							</svg>

						</div>

					<?php elseif ( 'bubble' === $transition ) : ?>

						<div id="msection-shape-<?php echo esc_attr( $id ); ?>" class="msection-shape <?php echo esc_attr( $position ); ?>" data-morph-open="M-7.312,0H15c0,0,66,113.339,66,399.5C81,664.006,15,800,15,800H-7.312V0z;M-7.312,0H100c0,0,0,113.839,0,400c0,264.506,0,400,0,400H-7.312V0z">

							<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 100 800" preserveAspectRatio="none">
								<path d="M-7.312,0H0c0,0,0,113.839,0,400c0,264.506,0,400,0,400h-7.312V0z"></path>
							</svg>

						</div>

					<?php elseif ( 'wave' === $transition ) : ?>

						<div id="msection-shape-<?php echo esc_attr( $id ); ?>" class="msection-shape <?php echo esc_attr( $position ); ?>" data-morph-open="M0,100h1000V0c0,0-136.938,0-224,0C583,0,610.924,0,498,0C387,0,395,0,249,0C118,0,0,0,0,0V100z">

						<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1000 100" preserveAspectRatio="none">
							<path d="M0,100h1000l0,0c0,0-136.938,0-224,0c-193,0-170.235-1.256-278-35C399,34,395,0,249,0C118,0,0,100,0,100L0,100z"></path>
						</svg>

						</div>

					<?php endif; ?>


			</div>

			<?php if ( 'selector' !== $trigger ) : ?>

				<div class="premium-msection-button-trig" data-float="<?php echo $is_float; ?>">
					<?php $this->render_trigger( $is_float ); ?>
				</div>

			<?php endif; ?>

		</div>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'overlay' ) ); ?>></div>

		<?php
	}

	/**
	 * Render Float Button
	 *
	 * @since 2.9.12
	 * @access protected
	 */
	protected function render_trigger( $float ) {

		$settings = $this->get_settings_for_display();

		$trigger = $settings['trigger'];

		if( in_array( $trigger, array('button', 'icon', 'svg') ) ) {

            if( 'button' === $trigger ) {
                $icon_type = $settings['icon_type'];
            } else {
                $icon_type = $trigger;
            }

			if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $icon_type ) || 'svg' === $icon_type ) {
				$this->add_render_attribute( 'icon', 'class', 'premium-msection-btn-icon' );
			}

			if ( 'yes' === $settings['draw_svg'] ) {

				$this->add_render_attribute(
					'container',
					'class',
					array(
						'elementor-invisible',
						'premium-drawer-hover',
					)
				);

				if ( 'icon' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', $settings['new_button_icon_selection']['value'] );

				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['svg_reverse'],
						'data-svg-loop'    => $settings['svg_loop'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-hover'   => $settings['svg_hover'],
						'data-svg-fill'    => $settings['svg_color'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => 0,
					)
				);

			} else {

				$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );

			}

            if( 'button' === $trigger ) {
                $effect_class = '';
                if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
                    $effect_class = Helper_Functions::get_button_class( $settings );
                }

                $this->add_render_attribute( 'trigger', 'class', $effect_class );
            }

			$this->add_render_attribute(
				'trigger',
				array(
					'class'     => array(
						'premium-msection-btn',
						$settings['premium_magic_section_button_size'],
					),
					'data-text' => $settings['premium_magic_section_button_text'],
				)
			);

		} elseif ( 'selector' === $trigger ) {

			$msection_settings['selector'] = $settings['css_selector'];

		}

		if ( 'lottie' === $trigger || ( 'button' === $trigger && 'lottie' === $icon_type ) ) {

			$handle = 'lottie' === $trigger ? 'trigger' : 'lottie_icon';

			$this->add_render_attribute(
				$handle,
				array(
					'class'               => array(
						'premium-modal-trigger-animation',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => 'url' === $settings['lottie_source'] ? $settings['lottie_url'] : $settings['lottie_file']['url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
					'data-lottie-hover'   => $settings['lottie_hover'],
				)
			);

		}

		if ( 'button' === $settings['trigger'] ) :
			?>
			<button <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>

			<?php
			if ( 'yes' === $settings['premium_magic_section_icon_switcher'] ) :

				if ( 'icon' === $icon_type ) :

					if ( 'yes' !== $settings['draw_svg'] ) :
						Icons_Manager::render_icon(
							$settings['new_button_icon_selection'],
							array(
								'class'       => array( 'premium-msection-btn-icon', 'premium-svg-nodraw' ),
								'aria-hidden' => 'true',
							)
						);
					else :
						?>
						<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
						<?php
					endif;

				elseif ( 'image' === $icon_type ) :

					$this->render_image_icon();

				elseif ( 'lottie' === $icon_type ) :
					?>

					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie_icon' ) ); ?>></div>

				<?php else : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
						<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
					</div>
					<?php
				endif;
			endif;
			?>

			<?php if ( ! empty( $settings['premium_magic_section_button_text'] ) ) : ?>
				<div class="premium-button-text-icon-wrapper">
					<span class="premium-msection-btn-text">
						<?php echo wp_kses_post( $settings['premium_magic_section_button_text'] ); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ( 'style6' === $settings['premium_button_hover_effect'] && 'yes' === $settings['mouse_detect'] ) : ?>
				<span class="premium-button-style6-bg"></span>
			<?php endif; ?>

			<?php if ( 'style8' === $settings['premium_button_hover_effect'] ) : ?>
				<?php echo Helper_Functions::get_btn_svgs( $settings['underline_style'] ); ?>
			<?php endif; ?>

			</button>
		<?php else : ?>

			<div class="premium-msection-btn">

				<?php
                if ( 'icon' === $trigger ) :

                    if ( 'yes' !== $settings['draw_svg'] ) :
						Icons_Manager::render_icon(
							$settings['new_button_icon_selection'],
							array(
								'class'       => array( 'premium-msection-btn-icon', 'premium-svg-nodraw' ),
								'aria-hidden' => 'true',
							)
						);
					else : ?>
						<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
						<?php
					endif;

                elseif ( 'svg' === $trigger ) : ?>
                    <div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
						<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
					</div>
				<?php elseif ( 'image' === $trigger ) :

					$this->render_image_icon();

				elseif ( 'lottie' === $trigger ) :
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>></div>
				<?php endif; ?>

			</div>
		<?php endif; ?>
		<?php

	}

	/**
	 * Render Image Icon
	 *
	 * @since 2.9.12
	 * @access protected
	 */
	protected function render_image_icon() {

		$settings = $this->get_settings_for_display();

		$trigger = $settings['trigger'];

		$alt = Control_Media::get_image_alt( $settings['premium_magic_section_custom_image'] );

		$this->add_render_attribute(
			'trigger_img',
			array(
				'src' => $settings['premium_magic_section_custom_image']['url'],
				'alt' => $alt,
			)
		);

		if ( 'image' === $trigger ) {

			$this->add_render_attribute( 'trigger_img', 'class', 'premium-msection-btn-icon' );

		}

		?>

			<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger_img' ) ); ?>>

		<?php

	}

}
