<?php

class ISSD_Image extends ET_Builder_Module {

	public $slug       = 'issd_image';
	public $vb_support = 'on';
    
    // Some details about the extension
	protected $module_credits = array(
		'module_uri' => 'https://www.boltonstudios.com',
		'author'     => 'Aaron Bolton',
		'author_uri' => 'https://www.boltonstudios.com',
	);
    
    // Initialize class
	public function init() {
		$this->name = esc_html__( 'Image at Size', 'issd_image' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
        $this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Image', 'issd_image' ),
					'link'         => esc_html__( 'Link', 'issd_image' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => esc_html__( 'Overlay', 'issd_image' ),
					'alignment'  => esc_html__( 'Alignment', 'issd_image' ),
					'width'      => array(
						'title'    => esc_html__( 'Sizing', 'issd_image' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'issd_image' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'issd_image' ),
						'priority' => 95,
					),
				),
			),
		);
		$this->advanced_fields = array(
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ),
				),
			),
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .et_pb_image_wrap",
							'border_styles' => "%%order_class%% .et_pb_image_wrap",
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main'         => '%%order_class%% .et_pb_image_wrap',
						'custom_style' => true,
					),
				),
			),
			'max_width'             => array(
				'options' => array(
					'max_width' => array(
						'depends_show_if' => 'off',
					),
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
		);
        $this->image_sizes = self::issd_get_image_dimensions();
	}
    
    // Module settings are defined in the get_fields() method.
	public function get_fields() {
        
        // Define Module Settings
		$output = array(
			'src' => array(
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'issd_image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'issd_image' ),
				'update_text'        => esc_attr__( 'Set As Image', 'issd_image' ),
				'hide_metadata'      => true,
				'affects'            => array(
					'alt',
					'title_text',
                    'size'
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'issd_image' ),
				'toggle_slug'        => 'main_content',
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'issd_image' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'issd_image' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'title_text' => array(
				'label'           => esc_html__( 'Image Title Text', 'issd_image' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'issd_image' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
            'size' => array(
                'label' => esc_html__( 'Image Size', 'issd_image' ),
                'type' => 'select',
				'depends_show_if' => 'on',
				'depends_on' => array(
					'src',
				),
                'option_category' => 'basic_option',
                'options' =>  $this->image_sizes, 'issd_image',
                'description' => esc_html__( 'Image cropping in the Visual Builder is approximated. Ensure thumbnail is available at desired size, then save and exit the Visual Builder to confirm the appearance of the image at selected size.' ),
                'tab_slug' => 'custom_css',
                'toggle_slug' => 'attributes'
            ),
			'show_in_lightbox' => array(
				'label'             => esc_html__( 'Open in Lightbox', 'issd_image' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'issd_image' ),
					'on'  => esc_html__( 'Yes', 'issd_image' ),
				),
				'default_on_front' => 'off',
				'affects'           => array(
					'url',
					'url_new_window',
					'use_overlay',
				),
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'issd_image' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Image Link URL', 'issd_image' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'affects'         => array(
					'use_overlay',
				),
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'issd_image' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'             => esc_html__( 'Image Link Target', 'issd_image' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'In The Same Window', 'issd_image' ),
					'on'  => esc_html__( 'In The New Tab', 'issd_image' ),
				),
				'default_on_front' => 'off',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'issd_image' ),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Image Overlay', 'issd_image' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'issd_image' ),
					'on'  => esc_html__( 'On', 'issd_image' ),
				),
				'default_on_front' => 'off',
				'affects'           => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'issd_image' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'issd_image' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'issd_image' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'issd_image' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'issd_image' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'issd_image' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'depends_show_if'     => 'on',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'issd_image' ),
			),
			'show_bottom_space' => array(
				'label'             => esc_html__( 'Show Space Below The Image', 'issd_image' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'      => esc_html__( 'Yes', 'issd_image' ),
					'off'     => esc_html__( 'No', 'issd_image' ),
				),
				'default_on_front' => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'margin_padding',
				'description'       => esc_html__( 'Here you can choose whether or not the image should have a space below it.', 'issd_image' ),
			),
			'align' => array(
				'label'           => esc_html__( 'Image Alignment', 'issd_image' ),
				'type'            => 'text_align',
				'option_category' => 'layout',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'default_on_front' => 'left',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'Here you can choose the image alignment.', 'issd_image' ),
				'options_icon'    => 'module_align',
			),
			'force_fullwidth' => array(
				'label'             => esc_html__( 'Force Fullwidth', 'issd_image' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'issd_image' ),
					'on'  => esc_html__( 'Yes', 'issd_image' ),
				),
				'default_on_front' => 'off',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
				'affects' => array(
					'max_width',
				),
			),
			'always_center_on_mobile' => array(
				'label'             => esc_html__( 'Always Center Image On Mobile', 'issd_image' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'issd_image' ),
					'off' => esc_html__( 'No', 'issd_image' ),
				),
				'default_on_front' => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'alignment',
			),
		);
        return $output;
	}
	public function get_alignment() {
		$alignment = isset( $this->props['align'] ) ? $this->props['align'] : '';
		return et_pb_get_alignment( $alignment );
	}
    // Finish the implementation of the render() method so that it will generate the module's HTML output based on its props.
	public function render( $attrs, $content = null, $render_slug ) {
        
        // Get selected image source URL.
		$src = $this->props['src'];
        $src_id = attachment_url_to_postid( $src );
        $src_url = parse_url( $src );
        $src_path = $src_url[ 'path' ];
        $src_path_parts = pathinfo( $src_path );
        $src_dir_name = $src_path_parts['dirname'];
        $src_file_name = $src_path_parts['filename'];
        $src_file_extension = $src_path_parts['extension'];
        $src_placeholder = "//placeholder.pics/svg/376x220/DEDEDE/555555/Size%20not%20found.";
        
        // Get selected size.
        $size = $this->props['size']; // Width: 376px. Height: 220px. (cropped).
        $size_explode = explode( '.', $size );
        if( $size_explode[1] ){
            // If $size does not contain multiple strings, no size was selected and the named size will be set to a default size.
            $size_width = (int) filter_var( $size_explode[0], FILTER_SANITIZE_NUMBER_INT );
            $size_height = (int) filter_var( $size_explode[1], FILTER_SANITIZE_NUMBER_INT );
        }
        
        // Get named size.
        // If no size was selected, set the named size to "medium".
        if( $size === "selectasize." ){
            $named_size = "large";
        } else if( $size === "fullsize." ){
            $named_size = "full";
        } else{
            $named_size = self::get_named_size( array( $size_width, $size_height ) );
        }
        
        // Get image source URL at named size.
        $src_url = wp_get_attachment_image_src( $src_id, $named_size );
        ( $src_url ) ? $src_url = $src_url[0] : $src_placeholder;
            
        $src_set = wp_get_attachment_image_srcset( $src_id, $named_size );
        ($src_set) ? $src_set_output = 'srcset="'. esc_attr( $src_set ) .'"' : $src_set_output ='';

        $src_sizes = wp_calculate_image_sizes( $named_size, $src_url, wp_get_attachment_metadata($src_id), $src_id );  
        ($src_sizes) ? $src_sizes_output = 'sizes="'. esc_attr( $src_sizes ) .'"' : $src_sizes_output ='';
            
        // Get selected image metadata
		$alt = $this->props['alt'];
		$title_text = $this->props['title_text'];
        
        // Get standard fields as in default Image module
		$url                     = $this->props['url'];
		$url_new_window          = $this->props['url_new_window'];
		$show_in_lightbox        = $this->props['show_in_lightbox'];
		$show_bottom_space       = $this->props['show_bottom_space'];
		$align                   = $this->get_alignment();
		$force_fullwidth         = $this->props['force_fullwidth'];
		$always_center_on_mobile = $this->props['always_center_on_mobile'];
		$overlay_icon_color      = $this->props['overlay_icon_color'];
		$hover_overlay_color     = $this->props['hover_overlay_color'];
		$hover_icon              = $this->props['hover_icon'];
		$use_overlay             = $this->props['use_overlay'];
		$animation_style         = $this->props['animation_style'];
        
		// Handle svg image behaviour
		$is_src_svg = isset( $src_file_extension ) ? 'svg' === $src_file_extension : false;

		// overlay can be applied only if image has link or if lightbox enabled
		$is_overlay_applied = 'on' === $use_overlay && ( 'on' === $show_in_lightbox || ( 'off' === $show_in_lightbox && '' !== $url ) ) ? 'on' : 'off';

		if ( 'on' === $force_fullwidth ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'max-width: 100% !important;',
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_image_wrap, %%order_class%% img',
				'declaration' => 'width: 100%;',
			) );
		}

		if ( ! $this->_is_field_default( 'align', $align ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'text-align: %1$s;',
					esc_html( $align )
				),
			) );
		}

		if ( 'center' !== $align ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'margin-%1$s: 0;',
					esc_html( $align )
				),
			) );
		}

		if ( 'on' === $is_overlay_applied ) {
			if ( '' !== $overlay_icon_color ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_overlay:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $overlay_icon_color )
					),
				) );
			}

			if ( '' !== $hover_overlay_color ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_overlay',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $hover_overlay_color )
					),
				) );
			}

			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		// Set display block for svg image to avoid disappearing svg image
		if ( $is_src_svg ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_image_wrap',
				'declaration' => 'display: block;',
			) );
		}

		$output = sprintf(
			'<span class="et_pb_image_wrap"><img src="%1$s" '. $src_set_output .' '. $src_sizes_output .' alt="%2$s"%3$s />%4$s</span>',
			esc_attr( $src_url ), // %1$s
			esc_attr( $alt ), // %2$s
			( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' ), // %3$s
			'on' === $is_overlay_applied ? $overlay_output : '' // %4$s
		);

		if ( 'on' === $show_in_lightbox ) {
			$output = sprintf( '<a href="%1$s" class="et_pb_lightbox_image" title="%3$s">%2$s</a>',
				esc_attr( $src ),
				$output,
				esc_attr( $alt )
			);
		} else if ( '' !== $url ) {
			$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		// Module classnames
		if ( ! in_array( $animation_style, array( '', 'none' ) ) ) {
			$this->add_classname( 'et-waypoint' );
		}

		if ( 'on' !== $show_bottom_space ) {
			$this->add_classname( 'et_pb_image_sticky' );
		}

		if ( 'on' === $is_overlay_applied ) {
			$this->add_classname( 'et_pb_has_overlay' );
		}

		if ( 'on' === $always_center_on_mobile ) {
			$this->add_classname( 'et_always_center_on_mobile' );
		}
        // Put it all together and return the output.
		$output = sprintf( '%1$s', $output );
        return $output;
	}
    /**
	 * Enqueues non-minified, hot reloaded javascript bundles.
	 *
	 * @since 3.1
	 */
	protected function _enqueue_debug_bundles() {
		// Frontend Bundle
		$site_url       = wp_parse_url( get_site_url() );
		$hot_bundle_url = "http://localhost:3000/static/js/frontend-bundle.js";

		wp_enqueue_script( "{$this->name}-frontend-bundle", $hot_bundle_url, $this->_bundle_dependencies['frontend'], $this->version, true );

		if ( et_core_is_fb_enabled() ) {
			// Builder Bundle
			$hot_bundle_url = "http://localhost:3000/static/js/builder-bundle.js";

			wp_enqueue_script( "{$this->name}-builder-bundle", $hot_bundle_url, $this->_bundle_dependencies['builder'], $this->version, true );
		}
	}
    /**
     * Original function from https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
     * Get size information for all currently-registered image sizes.
     * @global $_wp_additional_image_sizes
     * @uses   get_intermediate_image_sizes()
     * @return array $sizes Data for all currently-registered image sizes.
     */
     protected function get_image_sizes() {
        global $_wp_additional_image_sizes;
        $sizes = array();
        foreach ( get_intermediate_image_sizes() as $_size ) {
            if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
                $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
                $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
                $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $sizes[ $_size ] = array(
                    'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
                );
            }
        }
        return $sizes;
    }
    /**
     * Original function from https://wordpress.stackexchange.com/a/254064
     * Return the closest named size from an array of width and height values.
     *
     * Based off of WordPress's image_get_intermediate_size()
     * If the size matches an existing size then it will be used. If there is no
     * direct match, then the nearest image size larger than the specified size
     * will be used. If nothing is found, then the function will return false.
     * Uses get_image_sizes() to get all available sizes.
     *
     * @param  array|string $size   Image size. Accepts an array of width and height (in that order).
     * @return false|string $data   named image size e.g. 'thumbnail'.
     */
    protected function get_named_size( $size ) {
        $image_sizes = self::get_image_sizes();
        $data = array();
        // Find the best match when '$size' is an array.
        if ( is_array( $size ) ) {
            $candidates = array();
            foreach ( $image_sizes as $_size => $data ) {
                // If there's an exact match to an existing image size, short circuit.
                if ( $data['width'] == $size[0] && $data['height'] == $size[1] ) {
                    $candidates[ $data['width'] * $data['height'] ] = array( $_size, $data );
                    break;
                }
                // If it's not an exact match, consider larger sizes with the same aspect ratio.
                if ( $data['width'] >= $size[0] && $data['height'] >= $size[1] ) {
                    if ( wp_image_matches_ratio( $data['width'], $data['height'], $size[0], $size[1] ) ) {
                        $candidates[ $data['width'] * $data['height'] ] = array( $_size, $data );
                    }
                }
            }
            if ( ! empty( $candidates ) ) {
                // Sort the array by size if we have more than one candidate.
                if ( 1 < count( $candidates ) ) {
                    ksort( $candidates );
                }

                $data = array_shift( $candidates );
                $data = $data[0];
            /*
             * When the size requested is smaller than the thumbnail dimensions, we
             * fall back to the thumbnail size to maintain backwards compatibility with
             * pre 4.6 versions of WordPress.
             */
            } elseif ( ! empty( $image_sizes['thumbnail'] ) && $image_sizes['thumbnail']['width'] >= $size[0] && $image_sizes['thumbnail']['width'] >= $size[1] ) {
                $data = 'thumbnail';
            } else {
                return false;
            }
        } elseif ( ! empty( $image_sizes[ $size ] ) ) {
            $data = $size;
        }
        // If we still don't have a match at this point, return false.
        if ( empty( $data ) ) {
            return false;
        }
        return $data;
    }
    // Simple function to sort an array by a specific key. Maintains index association.
    // http://php.net/manual/en/function.sort.php
    protected function array_sort($array, $on, $order=SORT_ASC){
        $new_array = array();
        $sortable_array = array();
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }
    protected function issd_get_image_dimensions(){
        // Get the available image sizes.
        $issd_image_sizes = self::get_image_sizes();
        $issd_image_sizes = self::array_sort( $issd_image_sizes, 'width' ); // Sort sizes from smallest to largest.
        $issd_image_dimensions = array( 'selectasize.' => 'Select a size.' );
        foreach( $issd_image_sizes as $size ){
            $width = 'Width: ' . $size['width'] .'px.';
            $height = 'Height: '. $size['height'] .'px.';
            ( $size['crop'] ) ? $cropped = '(cropped).' : $cropped = '(best fit)';
            $size_str = $width .' '. $height .' '. $cropped;
            if( $size['width'] != 0 && $size['height'] != 0 ){
                $issd_image_dimensions += [ strtolower( preg_replace('/\s*/', '', $size_str) ) => $size_str ];
            }
        }
        $issd_image_dimensions += [ 'fullsize.' => 'Full size.' ];
        return $issd_image_dimensions;
    }
}

new ISSD_Image;