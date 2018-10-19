<?php

define ('REST_TIMEOUT', 15);

class WPRestSite
{
    public $base_url;
    public $api_root;
    public $name;
    public $description;
    
    private $namespaces;
    private $routes;
    private $authentication;
    
    
    public function __construct ( $base_url=false ) {
        if ( $base_url ) {
            $this->base_url = $base_url;
            $this->get_info();
        }
    }
    
    private function get_root_if_unset() {
        if ( !isset( $this->base_url ) )
            return ( new WP_Error( 'WPRestSite', 'Must have set base_url before calling.' ) );
        if ( !isset( $this->api_root ) ) $this->get_api_root();
        return true;
    }
    
    public function get_api_root () {
        if ( isset ($this->base_url) ) {
            // https://developer.wordpress.org/reference/functions/wp_remote_get/
            $response = wp_remote_get( $this->base_url, array( 'timeout' => REST_TIMEOUT ) );
            if ( is_array( $response ) && !is_wp_error( $response ) ) {
                if ( isset ( $response['headers'] ) )   {
                    $api_root_text = $response['headers']->offsetGet('link');
                    preg_match ( '/<(.*?)>; rel="https:\/\/api.w.org\/"/' , $api_root_text, $matches );
                    if ( count($matches) > 1 ) {
                        $this->api_root = $matches[1];
                        return ( $this->api_root );
                    }
                }
            }
        }
        return ( new WP_Error( 'get_api_root', 'Failed to get api root') );
    }
    
    public function get_info () {
        if ( !isset ( $this->base_url ) ) {
            return ( new WP_Error( 'get_info', 'Need base url to be set before calling get_info.' ) );
        }
        if ( !isset( $this->api_root ) ) $this->get_api_root();
        
        if ( isset( $this->api_root ) ) {
            $response = wp_remote_get( $this->api_root, array( 'timeout' => REST_TIMEOUT ) );
            if ( is_array( $response ) && !is_wp_error( $response ) ) {
                $response_obj = json_decode( $response['body'] );
                $this->name = $response_obj->name;
                $this->description = $response_obj->description;
                $this->namespaces = $response_obj->namespaces;
                $this->authentication = $response_obj->authentication;
                $this->routes = $response_obj->routes;
                return ( true );
            }
            else return ( new WP_Error( 'get_info', 'Could not get api_root data from remote site.' ) );
        }
        else return ( new WP_Error( 'get_info', 'Could not get api_root from remote site.' ) );
    }
    
    public function supports_endpoints () {
        if ( !isset( $this->namespaces ) )
            return ( new WP_Error( 'supports_endpoints', 'Need to get_info before calling.' ) );
        
        if ( !array_search( 'wp/v2', $this->namespaces ) ) return false;
        else return true;
        
    }
    
    public function get_type ( $route, $per_page=100, $max_pages=0 ) {
        $root_check = $this->get_root_if_unset();
        if ( is_wp_error( $root_check ) ) return $root_check;
        
        $route = preg_replace( '/^\//', '', $route );
        $page = 0;
        $total_pages = 0;
        $objects = array ();
        do {
            $page += 1;
            $params = '?' . 'per_page=' . $per_page . '&page=' . $page;
            $full_url = $this->api_root . $route . $params;
            $response = wp_remote_get( $full_url , array( 'timeout' => REST_TIMEOUT ) );
            if ( is_array( $response ) && !is_wp_error( $response ) ) {
                $objects = array_merge( $objects, json_decode( $response['body'] ) );
                $total_pages = $response['headers']->offsetGet('x-wp-totalpages');
            }
            elseif (is_wp_error( $response ) ) {
                if ( count($objects) == 0 ) return $response;
                else return $objects;
            }
            else {
                return $objects;
            }
            
        } while ( $page < $total_pages && ( $max_pages == 0 || $page < $max_pages ) );
        
        return $objects;
        
    }
    
    public function get_from_id ( $route, $id ) {
        if ( !isset( $this->api_root ) )
            return ( new WP_Error( 'get_object', 'Must have set api_root before calling.' ) );
        
        $route = preg_replace( '/^\//', '', $route );
        $full_url = $this->api_root . $route . '/' . $id;
        $response = wp_remote_get( $full_url , array( 'timeout' => REST_TIMEOUT ) );
        if ( is_array( $response ) && !is_wp_error( $response ) ) {
            return ( json_decode( $response['body'] ) );
        }
        elseif (is_wp_error( $response ) ) return ( $response );
        else return ( new WP_Error( 'get_object', 'Error retrieving from route.') );
  
    }
    
    public function get_endpoint ( $endpoint_url ) {
        if ( !isset( $this->base_url ) )
            return new WP_Error( 'WPRestSite', 'get_endpoint requires base_url to be set.');
        
        if ( !isset( $this->api_root ) ) $this->get_api_root();
        
        $endpoint_url = ltrim( $endpoint_url, '/');
        
        $full_url = $this->api_root . $endpoint_url;       
        $response = wp_remote_get ($full_url, array( 'timeout' => REST_TIMEOUT ) );
        if ( is_array( $response ) && !is_wp_error( $response ) ) {
            return ( json_decode( $response['body'] ) );
        }
        elseif (is_wp_error( $response ) ) return ( $response );
        else return ( new WP_Error( 'WPRestSite', 'Error retrieving from endpoint.') );
        
    }
    
}
?>