<?php

/**
 * Registers endpoints for the REST API.
 *
 * @link 		https://www.slushman.com
 * @since 		1.0.0
 * @package 	WpmenuRestApi\Includes
 * @author 		Slushman <chris@slushman.com>
 */

namespace WpmenuRestApi\Includes;

class Endpoints {

	/**
	 * Registers all the WordPress hooks and filters related to this class.
	 *
	 * @hooked 		init
	 * @since 		1.0.0
	 */
	public function hooks() {

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

	} // hooks()

	/**
	 * Format a menu item for REST API consumption.
	 * 
	 * Returns an empty object under two conditions:
	 * 		If first paramter ($menuItem) is not an object
	 * 		If the result returned from the wpmra_format_menu_item filter
	 * 			is not an object.
	 *
	 * @used-by 		test_format_menu_item()
	 * @since 			1.0.0
	 * @param 			object 		$menuItem 			The menu item
	 * @return 			object 							A formatted menu item for REST request
	 */
	public function format_menu_item( $menuItem ) {

		$formattedItem = new \stdClass();		

		if ( ! is_object( $menuItem ) ) { return $formattedItem; }

		$formattedItem->attr 				= $menuItem->attr_title;
		$formattedItem->classes 			= new \stdClass();
		$formattedItem->classes->array 		= $menuItem->classes;
		$formattedItem->classes->string 	= implode( ' ', $menuItem->classes );
		$formattedItem->description 		= $menuItem->description;
		$formattedItem->id 					= abs( $menuItem->ID );
		$formattedItem->object 				= $menuItem->object;
		$formattedItem->object_id 			= abs( $menuItem->object_id );
		$formattedItem->order 				= (int) $menuItem->menu_order;
		$formattedItem->parent 				= abs( $menuItem->menu_item_parent );
		$formattedItem->slug 				= $this->get_slug( $menuItem );
		$formattedItem->target 				= $menuItem->target;
		$formattedItem->title 				= $menuItem->title;
		$formattedItem->type 				= $menuItem->type;
		$formattedItem->type_label 			= $menuItem->type_label;
		$formattedItem->url 				= $menuItem->url;
		$formattedItem->xfn 				= $menuItem->xfn;
		
		/**
		 * The wpmra_format_menu_item filter.
		 * 
		 * @param 		object 		$formatted_item 		The formatted menu item.
		 * @param 		object 		$menuItem 				The menu item
		 */
		$formattedMenuItem = apply_filters( 'wpmra_format_menu_item', $formattedItem, $menuItem );

		return is_object( $formattedMenuItem ) ? $formattedMenuItem : new \stdClass();

	} // format_menu_item()

	/**
	 * Returns the REST endpoints URL for the site.
	 * 
	 * @used-by 		test_one_menu_get_menus()
	 * @uses 			get_namespace()
	 * @since 			1.0.0
	 * @param 			string 		$endpoint 		Optional. The endpoint location.
	 * @return 			string 						The REST endpoint URL.
	 */
	public function get_endpoint_url( $endpoint = '' ) {

		return trailingslashit( get_rest_url() . $this->get_namespace() . $endpoint );

	} // get_rest_url()

	/**
	 * Returns a single WordPress menu, based on the ID.
	 * 
	 * @used-by 		test_get_menu()
	 * @uses 			get_endpoint_url()
	 * @uses 			format_menu_item()
	 * @since 			1.0.0
	 * @param 			array 		$request 		The request data.
	 * @return 			array 						The requested WordPress menu.
	 */
	public function get_menu( $request ) {

		$id = (int) $request['id'];

		if ( ! is_int( $id ) ) { return array(); }

		$wpMenuObject 					= wp_get_nav_menu_object( $id );
		$restMenu 						= new \stdClass();
		$restMenu->ID 					= abs( $wpMenuObject->term_id );
		$restMenu->name        			= $wpMenuObject->name;
		$restMenu->slug        			= $wpMenuObject->slug;
		$restMenu->description 			= $wpMenuObject->description;
		$restMenu->count       			= abs( $wpMenuObject->count );
		$restMenu->items 				= $this->get_menu_items( $id );
		$restMenu->_links 				= new \stdClass();
		$restMenu->_links->collection 	= $this->get_endpoint_url( '/menus' );
		$restMenu->_links->self       	= $this->get_endpoint_url( '/menus' ) . $id;

		/**
		 * The wpmra_rest_menu filter.
		 * 
		 * @param 		object 		$restMenu 			The formatted menu.
		 * @param 		array 		$wpMenuObject 		The WP Menu object.
		 * @param 		array 		$request 			The REST request.
		 */
		$menu = apply_filters( 'wpmra_rest_menu', $restMenu, $wpMenuObject, $request );

		return is_object( $menu ) ? $menu : array();

	} // get_menu()

	/**
	 * Returns an array of menus items for the REST API.
	 * Requires the results from the wpmra_rest_menus filter
	 * to be an array.
	 * 
	 * @used-by 		test_get_menus()
	 * @uses 			get_endpoint_url()
	 * @since 			1.0.0
	 * @return 			array 		$menu
	 */
	public function get_menus() {

		$wpMenus = wp_get_nav_menus();

		if ( empty( $wpMenus ) || 1 > count( $wpMenus ) ) { return array(); }

		$restMenus = array();

		foreach ( $wpMenus as $wp_menu ) :

			$menu 						= new \stdClass();
			$menu->ID          			= $wp_menu->term_id;
			$menu->name        			= $wp_menu->name;
			$menu->slug        			= $wp_menu->slug;
			$menu->description 			= $wp_menu->description;
			$menu->count       			= $wp_menu->count;
			$menu->items 				= $this->get_menu_items( $wp_menu->term_id, $wp_menu );
			$menu->_links 				= new \stdClass();
			$menu->_links->collection 	= $this->get_endpoint_url( '/menus' );
			$menu->_links->self       	= $this->get_endpoint_url( '/menus' ) . $wp_menu->term_id;

			array_push( $restMenus, $menu );

		endforeach;

		/**
		 * The wpmra_rest_menus filter.
		 * 
		 * @param 		array 		$restMenus 		The REST menus.
		 * @param 		array 		$wpMenus 		The WP Menu objects.
		 */
		$menus = apply_filters( 'wpmra_rest_menus', $restMenus, $wpMenus );

		return is_array( $menus ) ? $menus : array();
		
	} // get_menus()

	/**
	 * Returns all the menu locations.
	 *
	 * @used-by 		test_get_menu_locations
	 * @since 			1.0.0
	 * @return 			array 						All registered menus locations
	 */
	public function get_menu_locations() {

		$locations        	= get_nav_menu_locations();
		$registeredMenus 	= get_registered_nav_menus();
		$restMenus       	= array();
		
		if ( $locations && $registeredMenus ) :
		
			foreach ( $registeredMenus as $slug => $label ) :
		
				if ( ! isset( $locations[$slug] ) ) { continue; }

				$menu 						= new \stdClass();
				$menu->slug 				= $slug;
				$menu->label 				= $label;
				$menu->items 				= $this->get_menu_items( $locations[$slug] );
				$menu->_links 				= new \stdClass();
				$menu->_links->collection 	= $this->get_endpoint_url( '/menu-locations' );
				$menu->_links->self			= $this->get_endpoint_url( '/menu-locations' ) . $slug;

				array_push( $restMenus, $menu );

			endforeach;

		endif;

		return $restMenus;

	} // get_menu_locations()

	/**
	 * Get menu for location.
	 * 
	 * wp_get_nav_menu_items() outputs a list that's already sequenced correctly.
	 * So the easiest thing to do is to reverse the list and then build our tree
	 * from the ground up
	 *
	 * @since 		1.0.0
	 * @param 		array 		$request 		The REST request.
	 * @return 		array 						The menu for the corresponding location
	 */
	public function get_menu_location( $request ) {

		$location   = $request['location'];
		$locations  = get_nav_menu_locations();
		
		if ( ! isset( $locations[$location] ) ) { return array(); }
		
		return $this->get_menu_items( $locations[$location] );
	
	} // get_menu_location()

	/**
	 * Returns the formatted menu items for the requested menuID.
	 * 
	 * @exits 		If $menuID is empty.
	 * @since 		1.0.0
	 * @param 		int 		$menuID 		The menu ID.
	 * @param 		obj 		$menuObj 		Optional. The menu object.
	 * @return 		array 						The formatted menu items.
	 */
	public function get_menu_items( $menuID ) {

		if ( ! is_int( $menuID ) ) { return array(); }

		$menuItems  = $menuID ? wp_get_nav_menu_items( $menuID ) : array();
		$revItems 	= array_reverse( $menuItems );
		$revMenu  	= array();
		
		foreach ( $revItems as $menuItem ) :

			$formattedItem = $this->format_menu_item( $menuItem );
		
			array_push( $revMenu, $formattedItem );
	
		endforeach;
		
		return array_reverse( $revMenu );
	
	} // get_menu_items()

	/**
	 * Returns the namespace string for this plugin's endpoints.
	 * 
	 * @used-by 		test_get_namespace()
	 * @since 			1.0.0
	 * @return 			string 		The namespace string.
	 */
	public function get_namespace() {

		return 'wpmenu/v1';

	} // get_namespace()

	/**
	 * Returns the slug for the object passed in.
	 * 
	 * @used-by 		test_get_slug()
	 * @since 			1.0.0
	 * @param 			obj 		$object 		The object
	 * @return 			string 						The slug.
	 */
	public function get_slug( $object ) {

		if ( ! is_object( $object ) ) { return ''; }

		$slug = '';

		if ( 'taxonomy' === $object->type && 'category' === $object->object ) {

			$category = get_category( $object->object_id );

			if ( is_object( $category ) ) {

				$slug = $category->slug;
				
			}

		} elseif ( 'taxonomy' === $object->type ) {

			$term = get_term( $object->object_id, $object->object );

			if ( is_object( $term ) ) {

				$slug = $term->slug;
				
			}

		} elseif ( 'post_type_archive' === $object->type ) {

			$slug = get_post_type_object( $object->object )->rewrite['slug'];

		} elseif ( 'post_type' === $object->type ) {

			$slug = basename( $object->url );

		}

		return $slug;

	} // get_slug()

	/**
	 * Registers routes for the WP API v2.
	 * 
	 * @hooked 		rest_api_init
	 * @since 		1.0.0
	 */
	public function register_routes() {

		register_rest_route( $this->get_namespace(), '/menus', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_menus' )
		) );

		register_rest_route( $this->get_namespace(), '/menus/(?P<id>\d+)', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_menu' )
		) );

		register_rest_route( $this->get_namespace(), '/menu-locations', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_menu_locations' )
		) );

		register_rest_route( $this->get_namespace(), '/menu-locations/(?P<location>[a-zA-Z0-9_-]+)', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_menu_location' )
		) );

	} // register_routes()

} // class