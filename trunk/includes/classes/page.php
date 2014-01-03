<?php  
    namespace includes\classes;
    /** 
     * This is our page object 
     * It is a seperate object to allow some interesting extra functionality to be added 
     * Some ideas: passwording pages, adding page specific css/js files, etc 
     */  
    class page {  
      
        // header elements
        private $css_files = array();
        private $css;  
        private $js_files = array();  
        private $js;
        private $js_override_files = array(); 
        private $js_override;
        // page elements  
        private $title = ''; 
        private $custom_html      = false;
		private $include_header   = false;
		private $include_footer   = false;
		private $include_template = 'template_main.php';   
          
        /** 
         * Constructor... 
         */  
        function __construct() {
        	$this->js_files[] = "includes/jquery-1.6.2.min.js";
  			$this->js_files[] = "includes/jquery-ui-1.8.16.custom.min.js";
  			$this->js_files[] = "includes/jquery.dataTables.min.js";
  			$this->js_files[] = "https://www.google.com/jsapi";
  			$this->js_files[] = "includes/common.js";
  			$this->css_files[] = DIR_WS_THEMES.'css/'.MY_COLORS.'/stylesheet.css';
  			$this->css_files[] = DIR_WS_THEMES.'css/'.MY_COLORS.'/jquery_datatables.css';
  			$this->css_files[] = DIR_WS_THEMES.'css/'.MY_COLORS.'/jquery-ui.css';
  			$this->css_files[] = DIR_WS_THEMES.'css/'.MY_COLORS.'/easyui.css';
  			$this->css_files[] = DIR_WS_THEMES.'css/icon.css';
        }
          
        /**
         * this will return the title
         * @return void
         */  
        
        public function getTitle() {  
            return $this->title;  
        }  
		
        /**
         * this will set the page title
         * @param string $title
         */
        
        public function setTitle( $title ) {  
            $this->title = $title;  
        }  

        public function print_js_includes(){
        	//first normal js files
        	foreach($this->js_files as $file){
        		echo "<script type='text/javascript' src='$file'></script>";
        	}
        	//then the override files
        	foreach($this->js_override_files as $file){
        		echo "<script type='text/javascript' src='$file'></script>";
        	}
        } 
        
        public function print_css_includes(){
        	foreach($this->css_files as $file){
        		echo "<link rel='stylesheet' type='text/css' href='$file' />";
        	}
        }
        /**
         * this will set the content
         * @param unknown_type $content
         */
        public function setContent( $content ) {  
            $this->content = $content;  
        }  
		
        /**
         * this will add content to the current content
         */
    	public function addContent( $content ) {  
            $this->content .= $content;  
        }
        /** 
         * Add a template bit to the page, doesnt actually add the content just yet 
         * @param String the tag where the template is added 
         * @param String the template file name 
         * @return void 
         */  
        public function addTemplateBit( $tag, $bit )  
        {  
            $this->bits[ $tag ] = $bit;  
        }  
          
        /** 
         * Get the template bits to be entered into the page 
         * @return array the array of template tags and template file names 
         */  
        public function getBits()  
        {  
            return $this->bits;  
        }  
          
        /** 
         * Gets a chunk of page content 
         * @param String the tag wrapping the block ( <!-- START tag --> block <!-- END tag --> ) 
         * @return String the block of content 
         */  
        public function getBlock( $tag )  
        {  
            preg_match ('#<!-- START '. $tag . ' -->(.+?)<!-- END '. $tag . ' -->#si', $this->content, $tor);  
              
            $tor = str_replace ('<!-- START '. $tag . ' -->', "", $tor[0]);  
            $tor = str_replace ('<!-- END '  . $tag . ' -->', "", $tor);  
              
            return $tor;  
        }  
		/**
		 * will return content
		 */
        
        public function getContent() {  
            return $this->content;  
        }  
        
    }  
    ?>  