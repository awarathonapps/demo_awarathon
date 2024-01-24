<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TestCache extends CI_Controller {

    public function index(){

        $this->load->driver('cache');

        if ($foo = $this->cache->file->get('foo2'))
            echo $foo.'<br />';
        else
            {
            echo 'Saving to the cache!<br />';
            $foo = 'foobarbaz! :D';
            $this->cache->file->save('foo2', $foo, 60);
            }
        // if($this->cache->clean())
        //     echo "clearing cache ";
        // else
        //     echo "Error clearing cache ";
    }
    
} 
