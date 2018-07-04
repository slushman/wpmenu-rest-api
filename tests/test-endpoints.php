<?php
/**
 * Class Test Endpoints.
 *
 * @package Wpmenu_Rest_Api
 */
class EndpointsTest extends WP_UnitTestCase {

	/**
	 * Configures WordPress for each test.
	 */
	public function setUp() {

		parent::setUp();

		update_option( 'permalink_structure', '/post/%postname%/' );

		$this->endpoints = new \WpmenuRestApi\Includes\Endpoints();
		$this->testcat = $this->factory->category->create( array( 'slug' => 'code-samples' ) );

		$this->categoryID1 = wp_create_category( 'Code Samples' );

		$this->menuName1 = 'TestMenu';
		$this->menuId1 = wp_create_nav_menu( $this->menuName1 );

		$testMenuItem1Data['menu-item-attr-title'] 	= 'Home';
		$testMenuItem1Data['menu-item-status'] 		= 'publish';
		$testMenuItem1Data['menu-item-target'] 		= '_blank';
		$testMenuItem1Data['menu-item-title'] 		= 'Blog';
		$testMenuItem1Data['menu-item-type'] 		= 'custom';
		$testMenuItem1Data['menu-item-url'] 		= 'https://www.slushman.com/';
		$this->testMenuItem1 						= wp_update_nav_menu_item( $this->menuId1, 0, $testMenuItem1Data );

		$testPage1Data['author'] 		= 1;
		$testPage1Data['post_content'] 	= 'content here';
		$testPage1Data['post_status'] 	= 'publish';
		$testPage1Data['post_title'] 	= 'Plugins';
		$testPage1Data['post_type'] 	= 'page';
		$testPage1 						= wp_insert_post( $testPage1Data );

		$testMenuItem2Data['menu-item-object'] 		= 'page';
		$testMenuItem2Data['menu-item-object-id'] 	= $testPage1;
		$testMenuItem2Data['menu-item-status'] 		= 'publish';
		$testMenuItem2Data['menu-item-title'] 		= 'Plugins';
		$testMenuItem2Data['menu-item-type'] 		= 'post_type';
		$testMenuItem2Data['menu-item-url'] 		= 'http://slushman.test/plugins/';
		$this->testMenuItem2 						= wp_update_nav_menu_item( $this->menuId1, 0, $testMenuItem2Data );

		$testMenuItem3Data['menu-item-status'] 		= 'publish';
		$testMenuItem3Data['menu-item-title'] 		= 'Contact';
		$testMenuItem3Data['menu-item-type'] 		= 'custom';
		$testMenuItem3Data['menu-item-url'] 		= 'mailto:chris@slushman.com';
		$this->testMenuItem3 						= wp_update_nav_menu_item( $this->menuId1, 0, $testMenuItem3Data );

		$testPost1Data['author'] 		= 1;
		$testPost1Data['post_content'] 	= 'content here';
		$testPost1Data['post_status'] 	= 'publish';
		$testPost1Data['post_title'] 	= 'Deploying a React App to Netlify';
		$testPost1 						= wp_insert_post( $testPost1Data );

		$testMenuItem4Data['menu-item-object'] 		= 'post';
		$testMenuItem4Data['menu-item-object-id'] 	= $testPost1;
		$testMenuItem4Data['menu-item-parent-id'] 	= $this->testMenuItem3;
		$testMenuItem4Data['menu-item-status'] 		= 'publish';
		$testMenuItem4Data['menu-item-title'] 		= 'Deploying a React App to Netlify';
		$testMenuItem4Data['menu-item-type'] 		= 'post_type';
		$testMenuItem4Data['menu-item-url'] 		= 'http://slushman.test/post/deploying-a-react-app-to-netlify/';
		$this->testMenuItem4 						= wp_update_nav_menu_item( $this->menuId1, 0, $testMenuItem4Data );

		$testPost2Data['author'] 		= 1;
		$testPost2Data['post_content'] 	= 'content here';
		$testPost2Data['post_status'] 	= 'publish';
		$testPost2Data['post_title'] 	= 'Troubleshooting WordPress AJAX';
		$testPost2 						= wp_insert_post( $testPost2Data );

		$testMenuItem5Data['menu-item-object'] 		= 'post';
		$testMenuItem5Data['menu-item-object-id'] 	= $testPost2;
		$testMenuItem5Data['menu-item-parent-id'] 	= $this->testMenuItem3;
		$testMenuItem5Data['menu-item-status'] 		= 'publish';
		$testMenuItem5Data['menu-item-title'] 		= 'Troubleshooting WordPress AJAX';
		$testMenuItem5Data['menu-item-type'] 		= 'post_type';
		$testMenuItem5Data['menu-item-url'] 		= 'http://slushman.test/post/troubleshooting-wordpress-ajax/';
		$this->testMenuItem5 						= wp_update_nav_menu_item( $this->menuId1, 0, $testMenuItem5Data );

		$testMenuItem6Data['menu-item-object'] 		= 'category';
		$testMenuItem6Data['menu-item-object-id'] 	= 5;
		$testMenuItem6Data['menu-item-status'] 		= 'publish';
		$testMenuItem6Data['menu-item-title'] 		= 'Code Samples';
		$testMenuItem6Data['menu-item-type'] 		= 'taxonomy';
		$testMenuItem6Data['menu-item-url'] 		= 'http://slushman.test/post/category/code-samples/';
		$this->testMenuItem6 						= wp_update_nav_menu_item( $this->menuId1, 0, $testMenuItem6Data );


		register_nav_menu( 'primary', __( 'Primary Menu', 'example' ) );
		register_nav_menu( 'social', __( 'Social Menu', 'example' ) );

		$this->locations 				= get_theme_mod('nav_menu_locations');
		$this->locations['primary'] 	= $this->menuId1;

		set_theme_mod( 'nav_menu_locations', $this->locations );

	} // setUp()

	/**
	 * Removes the testing configuration.
	 */
	public function tearDown() {

		parent::tearDown();

	} // tearDown()

	/**
	 * Tests that format_menu_item() returns a formatted object
	 * for a custom link to the home page menu item.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_customLink1()
	 * @uses 			format_menu_item()
	 */
	public function test_format_custom_homepage_link() {

		$customLinkResult1						= new \stdClass();
		$customLinkResult1->attr 				= 'Home';
		$customLinkResult1->classes->array[0] 	= 'test';
		$customLinkResult1->classes->string 	= 'test';
		$customLinkResult1->description 		= '';
		$customLinkResult1->id 					= 2791;
		$customLinkResult1->object 				= 'custom';
		$customLinkResult1->object_id 			= 2791;
		$customLinkResult1->order 				= 1;
		$customLinkResult1->parent 				= 0;
		$customLinkResult1->slug 				= '';
		$customLinkResult1->target 				= '_blank';
		$customLinkResult1->title 				= 'Blog';
		$customLinkResult1->type 				= 'custom';
		$customLinkResult1->type_label 			= 'Custom Link';
		$customLinkResult1->url 				= 'https://www.slushman.com/';
		$customLinkResult1->xfn 				= '';

		$customLink1 		= $this->return_customLink1();
		$testCustomLink1 	= $this->endpoints->format_menu_item( $customLink1 );

		$this->assertInternalType( 'object', $testCustomLink1 );
		$this->assertEquals( $testCustomLink1, $customLinkResult1 );

	} // test_format_custom_homepage_link()

	/**
	 * Tests that format_menu_item() returns a formatted object for a custom link
	 * menu item with an email address in the URL field.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_customLink2()
	 * @uses 			format_menu_item()
	 */
	public function test_format_email_menu_item() {

		$customLinkResult2 						= new \stdClass();
		$customLinkResult2->attr 				= '';
		$customLinkResult2->classes->array[0] 	= '';
		$customLinkResult2->classes->string 	= '';
		$customLinkResult2->description 		= '';
		$customLinkResult2->id 					= 2201;
		$customLinkResult2->object 				= 'custom';
		$customLinkResult2->object_id 			= 2201;
		$customLinkResult2->order 				= 3;
		$customLinkResult2->parent 				= 0;
		$customLinkResult2->slug 				= '';
		$customLinkResult2->target 				= '';
		$customLinkResult2->title 				= 'Contact';
		$customLinkResult2->type 				= 'custom';
		$customLinkResult2->type_label 			= 'Custom Link';
		$customLinkResult2->url 				= 'mailto:chris@slushman.com';
		$customLinkResult2->xfn 				= '';

		$customLink2 		= $this->return_customLink2();
		$testCustomLink2 	= $this->endpoints->format_menu_item( $customLink2 );

		$this->assertInternalType( 'object', $testCustomLink2 );
		$this->assertEquals( $testCustomLink2, $customLinkResult2 );

	} // test_format_email_menu_item()

	/**
	 * Tests that format_menu_item() returns a formatted object for 
	 * a page menu item.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_page1()
	 * @uses 			format_menu_item()
	 */
	public function test_format_page_menu_item() {

		$pageResult1 					= new \stdClass();
		$pageResult1->attr				= '';
		$pageResult1->classes->array[0] = '';
		$pageResult1->classes->string 	= '';
		$pageResult1->description 		= '';
		$pageResult1->id 				= 494;
		$pageResult1->object 			= 'page';
		$pageResult1->object_id 		= 402;
		$pageResult1->order 			= 2;
		$pageResult1->parent 			= 0;
		$pageResult1->slug 				= 'plugins';
		$pageResult1->target 			= '';
		$pageResult1->title 			= 'Plugins';
		$pageResult1->type 				= 'post_type';
		$pageResult1->type_label 		= 'Page';
		$pageResult1->url 				= 'https://www.slushman.com/plugins';
		$pageResult1->xfn 				= '';

		$page1 		= $this->return_page1();
		$testPage1 	= $this->endpoints->format_menu_item( $page1 );

		$this->assertInternalType( 'object', $testPage1 );
		$this->assertEquals( $testPage1, $pageResult1 );

	} // test_format_page_menu_item()

	/**
	 * Tests that format_menu_item() returns a formatted object for a
	 * post menu item.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_post1()
	 * @uses 			format_menu_item()
	 */
	public function test_format_post_menu_item() {

		$postResult1 					= new \stdClass();
		$postResult1->attr				= '';
		$postResult1->classes->array[0] = '';
		$postResult1->classes->string 	= '';
		$postResult1->description 		= '';
		$postResult1->id 				= 2847;
		$postResult1->object 			= 'post';
		$postResult1->object_id 		= 2824;
		$postResult1->order 			= 4;
		$postResult1->parent 			= 0;
		$postResult1->slug 				= 'deploying-a-react-app-to-netlify';
		$postResult1->target 			= '';
		$postResult1->title 			= 'Deploying a React App to Netlify';
		$postResult1->type 				= 'post_type';
		$postResult1->type_label 		= 'Post';
		$postResult1->url 				= 'http://slushman.test/post/deploying-a-react-app-to-netlify/';
		$postResult1->xfn 				= '';

		$post1 		= $this->return_post1();
		$testPost1 	= $this->endpoints->format_menu_item( $post1 );

		$this->assertInternalType( 'object', $testPost1 );
		$this->assertEquals( $testPost1, $postResult1 );

	} // test_format_post_menu_item()

	/**
	 * Tests that format_menu_item() returns a formatted object for a
	 * category menu item.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_category1()
	 * @uses 			format_menu_item()
	 */
	public function test_format_category_menu_item() {

		$categoryResult1 					= new \stdClass();
		$categoryResult1->attr				= '';
		$categoryResult1->classes->array[0] = '';
		$categoryResult1->classes->string 	= '';
		$categoryResult1->description 		= '';
		$categoryResult1->id 				= 2849;
		$categoryResult1->object 			= 'category';
		$categoryResult1->object_id 		= "$this->categoryID1";
		$categoryResult1->order 			= 5;
		$categoryResult1->parent 			= 0;
		$categoryResult1->slug 				= 'code-samples';
		$categoryResult1->target 			= '';
		$categoryResult1->title 			= 'Code Samples';
		$categoryResult1->type 				= 'taxonomy';
		$categoryResult1->type_label 		= 'Category';
		$categoryResult1->url 				= 'http://slushman.test/post/category/code-samples/';
		$categoryResult1->xfn 				= '';

		$category1 		= $this->return_category1();
		$testCategory1 	= $this->endpoints->format_menu_item( $category1 );

		$this->assertInternalType( 'object', $testCategory1 );
		$this->assertEquals( $testCategory1, $categoryResult1 );

	} // test_format_category_menu_item()

	/**
	 * Tests that format_menu_item() returns a formatted object for a
	 * taxonomy menu item.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			format_menu_item()
	 */
	public function test_format_taxonomy_menu_item() {

		// Taxonomy
		$testTaxonomy1 = $this->endpoints->format_menu_item( $this->taxonomy1 );

		$this->assertInternalType( 'object', $testTaxonomy1 );
		// $this->assertEquals( $testTaxonomy1, $this->taxonomyResult1 );

	} // test_format_taxonomy_menu_item()

	/**
	 * Tests that format_menu_item() returns an empty object when passed
	 * an empty string.
	 * 
	 * @covers 			Endpoints::format_menu_item()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			format_menu_item()
	 */
	public function test_format_empty_string() {

		// Test that it returns an empty object when first parameter is not an object
		$testEmptyParameter = $this->endpoints->format_menu_item( '' );

		$this->assertInternalType( 'object', $testEmptyParameter );
		$this->assertObjectNotHasAttribute( 'ID', $testEmptyParameter );

	} // test_format_empty_string()

	/**
	 * Tests that get_menu returns a menu object given the 
	 * test data.
	 * 
	 * @covers 			Endpoints::get_menu()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_formattedMenuItems()
	 * @uses 			get_menu()
	 */
	public function test_get_menu() {

		$menuObject 			= wp_get_nav_menu_object( 'TestMenu' );
		$formattedMenuItems 	= $this->return_formattedMenuItems();

		$resultMenu 					= new \stdClass();
		$resultMenu->ID 				= $menuObject->term_id;
		$resultMenu->name 				= $menuObject->name;
		$resultMenu->slug 				= $menuObject->slug;
		$resultMenu->description 		= $menuObject->description;
		$resultMenu->count 				= $menuObject->count;
		$resultMenu->items 				= $formattedMenuItems;
		$resultMenu->_links 			= new \stdClass();
		$resultMenu->_links->collection = 'http://example.org/wp-json/wpmenu/v1/menus/';
		$resultMenu->_links->self 		= 'http://example.org/wp-json/wpmenu/v1/menus/' . $menuObject->term_id;

		$check1 = $this->endpoints->get_menu( array( 'id' => $this->menuId1 ) );

		$this->assertInternalType( 'object', $check1 );
		$this->assertEquals( $check1, $resultMenu );

	} // test_get_menu()

	/**
	 * Tests that get_menus() returns an array
	 * with a menu object in it.
	 * 
	 * @covers 			Endpoints::get_menus()
	 * @expects 		array 		$result
	 * @since 			1.0.0
	 * @uses 			get_endpoint_url()
	 * @uses 			get_menus()
	 */
	public function test_get_menus() {

		$url 	= $this->endpoints->get_endpoint_url( '/menus' );
		$menus 	= $this->endpoints->get_menus();

		$result[0] 						= new \stdClass();
		$result[0]->_links 				= new \stdClass();
		$result[0]->ID          		= $this->menuId1;
		$result[0]->name        		= $this->menuName1;
		$result[0]->slug        		= strtolower( $this->menuName1 );
		$result[0]->description 		= '';
		$result[0]->count       		= 6;
		$result[0]->_links->collection 	= $url;
		$result[0]->_links->self 		= $url . $this->menuId1;

		$this->assertEquals( $menus, $result );

	} // test_get_menus()

	/**
	 * Tests that get_menu_location(0 returns the menu location
	 * object of the requestion menu location.
	 * 
	 * @covers 			Endpoints::get_menu_location()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			return_formattedMenuItems()
	 * @uses 			get_menu_location()
	 */
	public function test_menu_location_with_items() {

		$formattedTestMenuItems = $this->return_formattedMenuItems();
		$testMenuLocation1 		= 'primary';
		$check1 				= $this->endpoints->get_menu_location( array( 'location' => $testMenuLocation1 ) );

		$this->assertInternalType( 'array', $check1 );
		$this->assertEquals( $check1, $formattedTestMenuItems );

	} // test_menu_location_with_items()

	/**
	 * Tests that get_menu_location() returns a blank array when fetching
	 * an empty menu location.
	 * 
	 * @covers 			Endpoints::get_menu_location()
	 * @expects 		object
	 * @since 			1.0.0
	 * @uses 			get_menu_location()
	 */
	public function test_empty_menu_location() {

		// Should be empty since social contains no items.
		$testMenuLocation1 	= 'social';
		$check1 			= $this->endpoints->get_menu_location( array( 'location' => $testMenuLocation1 ) );

		$this->assertInternalType( 'array', $check1 );
		$this->assertEquals( $check1, array() );

	} // test_empty_menu_location()

	/**
	 * Tests that get_menu_locations() returns an array of menu locations
	 * and the menus assigned to them.
	 * 
	 * @covers 			Endpoints::get_menu_locations()
	 * @expects 		array
	 * @since 			1.0.0
	 * @uses 			get_menu_locations()
	 */
	public function test_returns_array_of_menu_locations() {

		$locations 	= get_registered_nav_menus();
		$navLocs 	= get_nav_menu_locations();

		$testMenuLocation1 									= 'primary';
		$menu1 												= wp_get_nav_menu_object( $navLocs[$testMenuLocation1] );
		$result1 											= array();
		$result1[$testMenuLocation1] 						= new \stdClass();
		$result1[$testMenuLocation1]->ID 					= $menu1->term_id;
		$result1[$testMenuLocation1]->label 				= $locations[$testMenuLocation1];
		$result1[$testMenuLocation1]->_links 				= new \stdClass();
		$result1[$testMenuLocation1]->_links->collection 	= 'http://example.org/wp-json/wpmenu/v1/menu-locations/';
		$result1[$testMenuLocation1]->_links->self			= 'http://example.org/wp-json/wpmenu/v1/menu-locations/primary';

		$check1 = $this->endpoints->get_menu_locations();

		$this->assertInternalType( 'array', $check1 );
		$this->assertEquals( $check1, $result1 );

	} // test_returns_array_of_menu_locations()

	/**
	 * Tests that get_namespace() returns 'wpmenu/v1'.
	 * 
	 * @covers 			Endpoints::get_namespace()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			get_namespace()
	 */
	public function test_returns_namespace() {

		$test = $this->endpoints->get_namespace();

		$this->assertEquals( 'wpmenu/v1', $test );

	} // test_returns_namespace()

	/**
	 * Tests that get_slug() returns  an empty string for a custom link.
	 * 
	 * @covers 			Endpoints::get_slug()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			return_customLink1()
	 * @uses 			get_slug()
	 */
	public function test_returns_custom_link_slug() {

		$customLink1 		= $this->return_customLink1();
		$testCustomLink1 	= $this->endpoints->get_slug( $customLink1 );

		$this->assertEquals( $testCustomLink1, '' );

	} // test_returns_custom_link_slug()

	/**
	 * Tests that get_slug() returns the expected slug for a page.
	 * 
	 * @covers 			Endpoints::get_slug()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			return_page1()
	 * @uses 			get_slug()
	 */
	public function test_returns_page_slug() {

		$page1 		= $this->return_page1();
		$testPage1 	= $this->endpoints->get_slug( $page1 );

		$this->assertInternalType( 'string', $testPage1 );
		$this->assertEquals( $testPage1, 'plugins' );

	} // test_returns_page_slug()

	/**
	 * Tests that get_slug() returns the expected slug for a post.
	 * 
	 * @covers 			Endpoints::get_slug()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			return_post1()
	 * @uses 			get_slug()
	 */
	public function test_returns_post_slug() {

		$post1 		= $this->return_post1();
		$testPost1 	= $this->endpoints->get_slug( $post1 );

		$this->assertInternalType( 'string', $testPost1 );
		$this->assertEquals( $testPost1, 'deploying-a-react-app-to-netlify' );

	} // test_returns_post_slug()

	/**
	 * Tests that get_slug() returns the expected slug for a category.
	 * 
	 * @covers 			Endpoints::get_slug()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			return_category1()
	 * @uses 			get_slug()
	 */
	public function test_returns_category_slug() {

		$category1 		= $this->return_category1();
		$testCategory1 	= $this->endpoints->get_slug( $category1 );

		$this->assertInternalType( 'string', $testCategory1 );
		$this->assertEquals( $testCategory1, 'code-samples' );

	} // test_returns_category_slug()
	
	/**
	 * Tests that get_endpoint_url() returns the endpoint of all the menus.
	 * 
	 * @covers 			Endpoints::get_endpoint_url()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			get_endpoint_url()
	 */
	public function test_returns_menus_endpoint() {

		$url1 = $this->endpoints->get_endpoint_url( '/menus' );

		$this->assertEquals( $url1, 'http://example.org/wp-json/wpmenu/v1/menus/' );

	} // test_returns_menus_endpoint()

	/**
	 * Tests that get_endpoint_url() returns the endpoint of a specific menu.
	 * 
	 * @covers 			Endpoints::get_endpoint_url()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			get_endpoint_url()
	 */
	public function test_returns_specific_menu_endpoint() {

		$url2 = $this->endpoints->get_endpoint_url( '/menus/127' );

		$this->assertEquals( $url2, 'http://example.org/wp-json/wpmenu/v1/menus/127/' );

	} // test_returns_specific_menu_endpoint()

	/**
	 * Tests that get_endpoint_url() returns the base endpoint URL if no parameters are passed.
	 * 
	 * @covers 			Endpoints::get_endpoint_url()
	 * @expects 		string
	 * @since 			1.0.0
	 * @uses 			get_endpoint_url()
	 */
	public function test_returns_root_endpoint() {

		$url = $this->endpoints->get_endpoint_url();

		$this->assertEquals( $url, 'http://example.org/wp-json/wpmenu/v1/' );

	} // test_returns_root_endpoint()



	/**
	 * Returns the customLink1 menu item object.
	 * 
	 * @since 		1.0.0
	 * @return 		object 		The customLink1 menu item object.
	 */
	protected function return_customLink1() {

		$customLink1 						= new \stdClass();
		$customLink1->ID 					= 2791;
		$customLink1->post_author 			= 2;
		$customLink1->post_date 			= '2018-05-23 16:02:51';
		$customLink1->post_date_gmt 		= '2018-05-23 21:02:51';
		$customLink1->post_content 			= '';
		$customLink1->post_title 			= 'Blog';
		$customLink1->post_excerpt 			= 'Home';
		$customLink1->post_status 			= 'publish';
		$customLink1->comment_status 		= 'closed';
		$customLink1->ping_status 			= 'closed';
		$customLink1->post_password 		= '';
		$customLink1->post_name 			= 'blog';
		$customLink1->to_ping 				= '';
		$customLink1->pinged 				= '';
		$customLink1->post_modified 		= '2018-05-23 17:05:53';
		$customLink1->post_modified_gmt 	= '2018-05-23 22:05:53';
		$customLink1->post_content_filtered = '';
		$customLink1->post_parent 			= 0;
		$customLink1->guid 					= 'https://www.slushman.com/?p=2791';
		$customLink1->menu_order 			= 1;
		$customLink1->post_type 			= 'nav_menu_item';
		$customLink1->post_mime_type 		= '';
		$customLink1->comment_count 		= 0;
		$customLink1->filter 				= 'raw';
		$customLink1->db_id 				= 2791;
		$customLink1->menu_item_parent 		= 0;
		$customLink1->object_id 			= 2791;
		$customLink1->object 				= 'custom';
		$customLink1->type 					= 'custom';
		$customLink1->type_label 			= 'Custom Link';
		$customLink1->title 				= 'Blog';
		$customLink1->url 					= 'https://www.slushman.com/';
		$customLink1->target 				= '_blank';
		$customLink1->attr_title 			= 'Home';
		$customLink1->description 			= '';
		$customLink1->classes[0] 			= 'test';
		$customLink1->xfn 					= '';

		return $customLink1;

	} // return_customLink1()

	/**
	 * Returns the customLink2 menu item object.
	 * 
	 * @since 		1.0.0
	 * @return 		object 		The customLink2 menu item object.
	 */
	protected function return_customLink2() {

		$customLink2 						= new \stdClass();
		$customLink2->ID 					= 2201;
		$customLink2->post_author 			= 2;
		$customLink2->post_date 			= '2015-04-17 21:09:11';
		$customLink2->post_date_gmt 		= '2015-04-18 02:09:11';
		$customLink2->post_content 			= '';
		$customLink2->post_title 			= 'Contact';
		$customLink2->post_excerpt 			= '';
		$customLink2->post_status 			= 'publish';
		$customLink2->comment_status 		= 'open';
		$customLink2->ping_status 			= 'closed';
		$customLink2->post_password 		= '';
		$customLink2->post_name 			= 'contact';
		$customLink2->to_ping 				= '';
		$customLink2->pinged 				= '';
		$customLink2->post_modified 		= '2018-06-27 09:12:43';
		$customLink2->post_modified_gmt 	= '2018-06-27 14:12:43';
		$customLink2->post_content_filtered = '';
		$customLink2->post_parent 			= 0;
		$customLink2->guid 					= 'https://www.slushman.com/?p=2201';
		$customLink2->menu_order 			= 3;
		$customLink2->post_type 			= 'nav_menu_item';
		$customLink2->post_mime_type 		= '';
		$customLink2->comment_count 		= 0;
		$customLink2->filter 				= 'raw';
		$customLink2->db_id 				= 2201;
		$customLink2->menu_item_parent 		= 0;
		$customLink2->object_id 			= 2201;
		$customLink2->object 				= 'custom';
		$customLink2->type 					= 'custom';
		$customLink2->type_label 			= 'Custom Link';
		$customLink2->title 				= 'Contact';
		$customLink2->url 					= 'mailto:chris@slushman.com';
		$customLink2->target 				= '';
		$customLink2->attr_title 			= '';
		$customLink2->description 			= '';
		$customLink2->classes[0] 			= '';
		$customLink2->xfn 					= '';

		return $customLink2;
	
	} // return_customLink2()

	/**
	 * Returns the page1 menu item object.
	 * 
	 * @since 		1.0.0
	 * @return 		object 		The page1 menu item object.
	 */
	protected function return_page1() {

		$page1 							= new \stdClass();
		$page1->ID 						= 494;
		$page1->post_author 			= 2;
		$page1->post_date 				= '2014-01-27 11:23:27';
		$page1->post_date_gmt 			= '2014-01-27 17:23:27';
		$page1->post_content 			= '';
		$page1->post_title 				= '';
		$page1->post_excerpt 			= '';
		$page1->post_status 			= 'publish';
		$page1->comment_status 			= 'open';
		$page1->ping_status 			= 'closed';
		$page1->post_password 			= '';
		$page1->post_name 				= '494';
		$page1->to_ping 				= '';
		$page1->pinged 					= '';
		$page1->post_modified 			= '2018-05-23 17:05:53';
		$page1->post_modified_gmt 		= '2018-05-23 22:05:53';
		$page1->post_content_filtered 	= '';
		$page1->post_parent 			= 0;
		$page1->guid 					= 'https://www.slushman.com/2014/01/27/494/';
		$page1->menu_order 				= 2;
		$page1->post_type 				= 'nav_menu_item';
		$page1->post_mime_type 			= '';
		$page1->comment_count 			= 0;
		$page1->filter 					= 'raw';
		$page1->db_id 					= 494;
		$page1->menu_item_parent 		= 0;
		$page1->object_id 				= 402;
		$page1->object 					= 'page';
		$page1->type 					= 'post_type';
		$page1->type_label 				= 'Page';
		$page1->title 					= 'Plugins';
		$page1->url 					= 'https://www.slushman.com/plugins';
		$page1->target 					= '';
		$page1->attr_title 				= '';
		$page1->description 			= '';
		$page1->classes[0] 				= '';
		$page1->xfn 					= '';

		return $page1;

	} // return_page1()

	/**
	 * Returns the post1 menu item object.
	 * 
	 * @since 		1.0.0
	 * @return 		object 		The post1 menu item object.
	 */
	protected function return_post1() {

		$post1 							= new \stdClass();
		$post1->ID 						= 2847;
		$post1->post_author 			= 2;
		$post1->post_date 				= '2018-06-27 09:06:10';
		$post1->post_date_gmt 			= '2018-06-27 14:06:10';
		$post1->post_content 			= '';
		$post1->post_title 				= '';
		$post1->post_excerpt 			= '';
		$post1->post_status 			= 'publish';
		$post1->comment_status 			= 'closed';
		$post1->ping_status 			= 'closed';
		$post1->post_password 			= '';
		$post1->post_name 				= '2847';
		$post1->to_ping 				= '';
		$post1->pinged 					= '';
		$post1->post_modified 			= '2018-06-27 09:06:10';
		$post1->post_modified_gmt 		= '2018-06-27 14:06:10';
		$post1->post_content_filtered 	= '';
		$post1->post_parent 			= 0;
		$post1->guid 					= 'https://www.slushman.com/?p=2847';
		$post1->menu_order 				= 4;
		$post1->post_type 				= 'nav_menu_item';
		$post1->post_mime_type 			= '';
		$post1->comment_count 			= 0;
		$post1->filter 					= 'raw';
		$post1->db_id 					= 2847;
		$post1->menu_item_parent 		= 0;
		$post1->object_id 				= 2824;
		$post1->object 					= 'post';
		$post1->type 					= 'post_type';
		$post1->type_label 				= 'Post';
		$post1->title 					= 'Deploying a React App to Netlify';
		$post1->url 					= 'http://slushman.test/post/deploying-a-react-app-to-netlify/';
		$post1->target 					= '';
		$post1->attr_title 				= '';
		$post1->description 			= '';
		$post1->classes[0] 				= '';
		$post1->xfn 					= '';

		return $post1;

	} // return_post1()

	/**
	 * Returns the category1 menu item object.
	 * 
	 * @since 		1.0.0
	 * @return 		object 		The category1 menu item object.
	 */
	protected function return_category1() {

		$categoryID1 						= $this->factory->category->create( array( 'taxonomy' => 'category', 'name' => 'Code Samples' ) );
		$category1 							= new \stdClass();
		$category1->ID 						= 2849;
	    $category1->post_author 			= 2;
	    $category1->post_date 				= '2018-06-27 09:12:43';
	    $category1->post_date_gmt 			= '2018-06-27 14:12:43';
	    $category1->post_content 			= '';
	    $category1->post_title 				= '';
	    $category1->post_excerpt 			= '';
	    $category1->post_status 			= 'publish';
	    $category1->comment_status 			= 'closed';
	    $category1->ping_status 			= 'closed';
	    $category1->post_password 			= '';
	    $category1->post_name 				= 2849;
	    $category1->to_ping 				= '';
	    $category1->pinged 					= '';
	    $category1->post_modified 			= '2018-06-27 09:12:43';
	    $category1->post_modified_gmt 		= '2018-06-27 14:12:43';
	    $category1->post_content_filtered 	= '';
	    $category1->post_parent 			= 0;
	    $category1->guid 					= 'http://slushman.test/?p=2849';
	    $category1->menu_order 				= 5;
	    $category1->post_type 				= 'nav_menu_item';
	    $category1->post_mime_type 			= '';
	    $category1->comment_count 			= 0;
	    $category1->filter 					= 'raw';
	    $category1->db_id 					= 2849;
	    $category1->menu_item_parent 		= 0;
	    $category1->object_id 				= $this->categoryID1;
	    $category1->object 					= 'category';
	    $category1->type 					= 'taxonomy';
	    $category1->type_label 				= 'Category';
	    $category1->title 					= 'Code Samples';
	    $category1->url 					= 'http://slushman.test/post/category/code-samples/';
	    $category1->target 					= '';
	    $category1->attr_title 				= '';
	    $category1->description 			= '';
		$category1->classes[0] 				= '';
	    $category1->xfn 					= '';

		return $category1;

	} // return_category1()

	/**
	 * Returns formatted test menu items.
	 * 
	 * @since 		1.0.0
	 * @return 		array 		Formatted test menu items.
	 */
	protected function return_formattedMenuItems() {

		$testMenuItems 			= wp_get_nav_menu_items( $this->menuId1 );
		$formattedTestMenuItems = array();

		foreach ( $testMenuItems as $testMenuItem ) {

			$formattedItem = $this->endpoints->format_menu_item( $testMenuItem );

			array_push( $formattedTestMenuItems, $formattedItem );

		}

		return $formattedTestMenuItems;
		
	} // return_formattedMenuItems()

} // class
